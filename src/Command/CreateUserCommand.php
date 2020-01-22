<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'finance:user:create';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct(null);
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new User in the System')
            ->addArgument('firstName', InputArgument::REQUIRED, 'The firstname of the User.')
            ->addArgument('lastName', InputArgument::REQUIRED, 'The lastname of the User.')
            ->addArgument('email', InputArgument::REQUIRED, 'The E-Mail of the User.')
            ->addArgument('password', InputArgument::REQUIRED, 'The Password of the User.')
            ->setHelp('This command allows you to create a User to the System. Mostly used, when you clean install the Software');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setPassword($this->encoder->encodePassword($user, $input->getArgument('password')));
        $user->setFirstName($input->getArgument('firstName'));
        $user->setLastName($input->getArgument('lastName'));
        $user->setEmail($input->getArgument('email'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln([
            'Financy User Creator',
            '====================',
            'User created',
            '====================',
            '',
        ]);
        $output->writeln('FirstName: ' . $input->getArgument('firstName'));
        $output->writeln('LastName: ' . $input->getArgument('lastName'));
        $output->writeln('E-Mail: ' . $input->getArgument('email'));
        $output->writeln('Password: ' . $input->getArgument('password'));

        return 0;
    }
}