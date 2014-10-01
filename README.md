carlosbuenosvinos/ddd
=====================

DDD helper classes

## Application Services

### Application Service Interface

Consider an Application Service that registers a new user in your application. 

    $signInUserService = new SignInUserService(
        $em->getRepository('MyBC\Domain\Model\User\User')
    );
    
    $response = $signInUserService->execute(
        new SignInUserRequest(
            'carlos.buenosvinos@gmail.com',
            'thisisnotasecretpassword'
        )
    )

    $newUserCreated = $response->getUser();
    //...

We need to pass in the constructor all the dependencies. In this case, the
User repository. As DDD explains, the Doctrine repository is implementing a generic
interface for User repositories.

    <?php
    
    namespace Ddd\Application\Service\User;
    
    use Ddd\Domain\Model\User\User;
    use Ddd\Domain\Model\User\UserAlreadyExistsException;
    use Ddd\Domain\Model\User\UserRepository;
    
    /**
     * Class SignInUserService
     * @package Ddd\Application\Service\User
     */
    class SignInUserService implements Service
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

I suggest to make your Application Services implement the following interface following
the command pattern.

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

## Transactions ##

Application Services should manage transactions when dealing with database persistence
strategies. In order to manage it cleanly, I provide an Application Service decorator
that wraps an Application Service an executes it inside a transactional boundary.

The decorator is the ```Ddd\Application\Service\TransactionalApplicationService``` class.
In order to create one, you need the non transactional Application Service and a Transactional
Session. We provide different types of Transactional Sessions. See how to do it with Doctrine.

## Doctrine Transactional Application Services  ##

    /** @var EntityManager $em */
    $txSignInUserService = new TransactionalApplicationService(
        new SignInUserService(
            $em->getRepository('MyBC\Domain\Model\User\User')
        ),
        $em
    );
    
    $response = $txSignInUserService->execute();
