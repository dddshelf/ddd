carlosbuenosvinos/ddd
=====================

[![Build Status](https://secure.travis-ci.org/dddinphp/ddd.svg?branch=master)](http://travis-ci.org/dddinphp/ddd)

This library will help you with typical DDD scenarios, for now:
* Application Services Interface
* Transactional Application Services with Doctrine and ADODb
* Data Transformers Interface
* No Transformer Data Transformers
* Domain Event Interface
* Event Store Interface
* Event Store Doctrine Implementation
* Domain Event Publishing Service
* Messaging Producer Interface
* Messaging Producer RabbitMQ Implementation

# Sample Projects

There are some projects developed using carlosbuenosvinos/ddd library. Check some of them to see how to use it:
* [Last Wishes](https://github.com/dddinphp/last-wishes): Actions to run, such as tweet, send emails, etc. in case anything happen to you.

# Application Services

## Application Service Interface

Consider an Application Service that registers a new user in your application. 

```php
<?php
    $signInUserService = new SignInUserService(
        $em->getRepository('MyBC\Domain\Model\User\User')
    );
    
    $response = $signInUserService->execute(
        new SignInUserRequest(
            'carlos.buenosvinos@gmail.com',
            'thisisnotasecretpassword'
        )
    );

    $newUserCreated = $response->getUser();
    //...
```

We need to pass in the constructor all the dependencies. In this case, the User repository. As DDD explains, the Doctrine repository is implementing a generic interface for User repositories.

```php
<?php
    
    namespace MyBC\Application\Service\User;
    
    use MyBC\Domain\Model\User\User;
    use MyBC\Domain\Model\User\UserAlreadyExistsException;
    use MyBC\Domain\Model\User\UserRepository;
    
    use Ddd\Application\Service\ApplicationService;
    
    /**
     * Class SignInUserService
     * @package MyBC\Application\Service\User
     */
    class SignInUserService implements ApplicationService
    {
        /**
         * @var UserRepository
         */
        private $userRepository;
    
        /**
         * @param UserRepository $userRepository
         */
        public function __construct(UserRepository $userRepository)
        {
            $this->userRepository = $userRepository;
        }
    
        /**
         * @param SignInUserRequest $request
         * @return SignInUserResponse
         * @throws UserAlreadyExistsException
         */
        public function execute($request = null)
        {
            $email = $request->email();
            $password = $request->password();
    
            $user = $this->userRepository->userOfEmail($email);
            if (null !== $user) {
                throw new UserAlreadyExistsException();
            }
    
            $user = new User(
                $this->userRepository->nextIdentity(),
                $email,
                $password
            );
    
            $this->userRepository->persist($user);
    
            return new SignInUserResponse($user);
        }
    }
```
I suggest to make your Application Services implement the following interface following the command pattern.

```php
<?php
    /**
     * Interface ApplicationService
     * @package Ddd\Application\Service
     */
    interface ApplicationService
    {
        /**
         * @param $request
         * @return mixed
         */
        public function execute($request = null);
    }
```
## Transactions

Application Services should manage transactions when dealing with database persistence strategies. In order to manage it cleanly, I provide an Application Service decorator that wraps an Application Service an executes it inside a transactional boundary.

The decorator is the ```Ddd\Application\Service\TransactionalApplicationService``` class. In order to create one, you need the non transactional Application Service and a Transactional Session. We provide different types of Transactional Sessions. See how to do it with Doctrine.

### Doctrine Transactional Application Services

For the Doctrine Transactional Session, pass the EntityManager instance.
```php
<?php
    /** @var EntityManager $em */
    $txSignInUserService = new TransactionalApplicationService(
        new SignInUserService(
            $em->getRepository('MyBC\Domain\Model\User\User')
        ),
        new DoctrineSession($em)
    );
    
    $response = $txSignInUserService->execute(
        new SignInUserRequest(
            'carlos.buenosvinos@gmail.com',
            'thisisnotasecretpassword'
        )
    );
    
    $newUserCreated = $response->getUser();
    //...
```
As you can see, the use case creation and execution is the same as the non transactional, the only difference is the decoration with the Transactional Application Service.

As a collateral benefit, the Doctrine Session manages internally the ```flush``` method, so you don't need to add a ```flush``` in your Domain neither your infrastructure.

## Asynchronous AMQP listeners

This library is capable to support asynchronous messaging in order to make Bounded Context capable to listen to other Bounded Context's events in an efficient way. The base for this is the both the **[amqp](https://pecl.php.net/package/amqp)** and the **[react's event loop](https://github.com/reactphp/event-loop)**. In addition, to support more efficient event loopings, we recommend the installation of one of this extensions

* **[libevent](http://php.net/manual/en/book.libevent.php)**
* **[libev](http://php.net/manual/en/intro.ev.php)**
* **[event](http://php.net/manual/en/book.event.php)**

The usage of any of this extensions is handled by ReactPHP's *event-loop* in a totally transparent way.

### Example

Supose we need to listen to the ```Acme\Billing\DomainModel\Order\OrderWasCreated``` event triggered via messaging from  another bounded context. The following, is an example of a AMQP exchange listener that listents to the ```Acme\Billing\DomainModel\Order\OrderWasCreated``` event.

```php
<?php

namespace Acme\Inventory\Infrastructure\Messaging\Amqp;

use stdClass;
use AMQPQueue;
use JMS\Serializer\Serializer;
use League\Tactician\CommandBus;
use React\EventLoop\LoopInterface;
use Ddd\Infrastructure\Application\Notification\AmqpExchangeListener;

class OrderWasCreatedListener extends AmqpExchangeListener
{
    private $commandBus;
    
    public function __construct(AMQPQueue $queue, LoopInterface $loop, Serializer $serializer, CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        
        parent::construct($queue, $loop, $serializer);
    }
    
    /**
     * This method will be responsible to decide whether this listener listens to an specific
     * event type or not, given an event type name
     *
     * @param string $typeName
     *
     * @return bool
     */
    protected function listensTo($typeName)
    {
        return 'Acme\Billing\DomainModel\Order\OrderWasCreated' === $typeName;
    }

    /**
     * The action to perform
     *
     * @param stdClass $event
     *
     * @return void
     */
    protected function handle($event)
    {
        $this->commandBus->handle(new CreateOrder(
            $event->order_id,
            // ...
        ));
    }
}
```

And this is a possible command to create AMQP workers

```php
<?php

namespace AppBundle\Command;

use AMQPConnection;
use AMQPChannel;
use React\EventLoop\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\Serializer\Serializer;
use League\Tactician\CommandBus;

class OrderWasCreatedWorkerCommand extends Command
{
    private $serializer;
    private $commandBus;
    
    public function __construct(Serializer $serializer, CommandBus $commandBus)
    {
        $this->serializer = $serializer;
        $this->commandBus = $commandBus;
        
        parent::__construct();
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPConnection([
            'host' => 'example.host',
            'vhost' => '/',
            'port' => 5763,
            'login' => 'user',
            'password' => 'password'
        ]);
        $connection->connect();
        
        $queue = new AMQPQueue(new AMQPChannel($connection));
        $queue->setName('events');
        $queue->setFlags(AMQP_NOPARAM);
        $queue->declareQueue();
        
        $loop = Factory::create();
        
        $listener = new OrderWasCreatedListener(
            $queue,
            $loop,
            $serializer,
            $this->commandBus
        );
        
        $loop->run();
    }
}
```

## AMQP Message producer

The intention of the AMQP message producer is to be composed in some other class. The following is an example of the usage of the AMQP message producer.

```php
<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Ddd\Infrastructure\Application\Notification\AmqpMessageProducer;
use Ddd\Application\Notification\NotificationService;

$connection = new AMQPConnection([
    'host' => 'example.host',
    'vhost' => '/',
    'port' => 5763,
    'login' => 'user',
    'password' => 'password'
]);
$connection->connect();

$exchange = new AMQPExchange(new AMQPChannel($connection));
$exchange->setName('events');
$exchange->declare();

$config = Setup::createYAMLMetadataConfiguration([__DIR__."/src/Infrastructure/Application/Persistence/Doctrine/Config"], false);
$entityManager = EntityManager::create(['driver' => 'pdo_sqlite', 'path' => __DIR__ . '/db.sqlite'], $config);

$eventStore = $entityManager->getRepository('Ddd\Domain\Event\StoredEvent');
$publishedMessageTracker = $entityManager->getRepository('Ddd\Domain\Event\PublishedMessage');
$messageProducer = new AmqpMessageProducer($exchange);

$notificationService = new NotificationService(
    $eventStore,
    $publishedMessageTracker,
    $messageProducer
);

$notificationService->publish(/** ... **/);
```
