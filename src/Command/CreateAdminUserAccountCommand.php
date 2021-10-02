<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserAccountCommand extends Command
{
    protected static $defaultName = 'app:create-admin-account';

    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private ValidatorInterface $validator;

    /**
     * CreateAdminUserAccount constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ValidatorInterface $validator
     * @param string|null $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ValidatorInterface $validator,
        string $name = null
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new admin user.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a admin user.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The e-mail of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $defaultValue = 'admin';
            $showDefault = "<comment>${defaultValue}</comment>";
            $questionString = sprintf("<info>Username of the admin panel user</info>: [%s] \n > ", $showDefault);

            $question = new Question($questionString, $defaultValue);

            $questions['username'] = $question;
        }

        if (!$input->getArgument('email')) {
            $question = new Question("<info>Email of the admin panel user</info>: \n > ");

            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question("<info>Password of the admin panel user</info>: \n > ");
            $question->setHiddenFallback(false);
            $question->setHidden(true);

            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->entityManager;
        $userPasswordEncoder = $this->userPasswordEncoder;
        $validator = $this->validator;

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $output->writeln([
            'Admin User Creator',
            '============',
            '',
        ]);

        $adminUser = new AdminUser($email, $username, '');
        $adminUser->setRoles(['ROLE_ADMIN']);

        $encodedPassword = $userPasswordEncoder->encodePassword($adminUser, $password);
        $adminUser->setPassword($encodedPassword);

        $errors = $validator->validate($adminUser);

        foreach ($errors as $error) {
            $output->writeln("<error> {$error->getMessage()} </error> \n");
        }

        $em->persist($adminUser);
        $em->flush();

        $output->writeln("User ${username} has been created!");
    }
}
