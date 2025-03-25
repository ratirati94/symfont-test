<?php

namespace App\Command;

use App\Repository\NewsRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsCommand(
    name: 'app:send-weekly-top-news',
    description: 'Sends top 10 news with most comments to a designated email address'
)]
#[AsPeriodicTask('7 days', schedule: 'default')]
class SendWeeklyTopNewsCommand extends Command
{
    public function __construct(
        private readonly NewsRepository        $newsRepo,
        private readonly MailerInterface       $mailer,
        private readonly ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $topNews = $this->newsRepo->findTopNewsWithMostComments(10);

        if (empty($topNews)) {
            $output->writeln('No news found.');
            return Command::SUCCESS;
        }

        $content = "📰 Weekly Top 10 News by Comments:\n\n";
        foreach ($topNews as $news) {
            $content .= sprintf(
                "- %s (%d comments)\n",
                $news['title'],
                $news['comment_count']
            );
        }
        $weeklyReportEmailAddress = $this->params->get('weekly_report_email');

        $email = (new Email())
            ->from('no-reply@symfony.com')
            ->to($weeklyReportEmailAddress)
            ->subject('🧾 Weekly Top News Report')
            ->text($content);

        $this->mailer->send($email);

        $output->writeln('Weekly top news sent!');
        return Command::SUCCESS;
    }
}

