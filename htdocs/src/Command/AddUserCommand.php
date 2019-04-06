<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddUserCommand extends Command
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     * @param EntityManager  $entityManager
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('user:add')
            ->setDescription('Adds a new User')
            ->addArgument('email', InputArgument::REQUIRED, 'The email must be an unique value.')
            ->addArgument('password', InputArgument::REQUIRED, 'The userpassword.');
    }

    /**
     * Run the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (!$input->getArgument('email')) {
                throw new RuntimeException('Empty email', 1);
            }

            if (!$input->getArgument('password')) {
                throw new RuntimeException('Empty password', 1);
            }

            $output->writeln('Checking if there is a user with the same email.');
            if ($this->userRepository->findBy(['email' => $input->getArgument('email')])) {
                throw new RuntimeException('There is a user with this email');
            }

            /** @var User $user */
            $user = new User();
            $user->setEmail($input->getArgument('email'))
                ->setPassword($this->passwordEncoder->encodePassword($user, $input->getArgument('password')));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln('User Added');
        } catch (RuntimeException $exception) {
            $output->writeln($exception->getMessage());
        }
    }
}
