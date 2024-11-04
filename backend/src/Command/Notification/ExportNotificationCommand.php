<?php

namespace App\Command\Notification;

use App\Business\Email\EmailNotificationService;
use App\Business\Export\BestellungExportHelper;
use App\Repository\BestellungRepository;
use Crunz\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportNotificationCommand extends Command
{
    protected static $defaultName = 'app:notify-export';
    protected static $defaultDescription = 'Sends Emails with export of orders as attachment';

    public function __construct(
        private readonly BestellungRepository $bestellungRepository,
        private readonly BestellungExportHelper $bestellungExportHelper,
        private readonly EmailNotificationService $emailNotificationService,
        private readonly ?string $exportMailRecipients
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if (empty($this->exportMailRecipients)) {
                $output->writeln('No recipients specified in ENV variable EXPORT_MAIL_RECIPIENTS, aborting.');
                return Command::SUCCESS;
            }

            $filterParams = [];
            $filterParams['createdAfter'] = new \DateTime('-1 week');

            $bestellungen = $this->bestellungRepository->getByDepartment($filterParams);
            $bestellungExportPath = $this->bestellungExportHelper->generateExport($bestellungen, true);

            $attachments = [$bestellungExportPath];

            $datumString = (new \DateTime())->format('d.m.Y');
            $emailSubject = 'Bestellungen Export: ' . $datumString;
            $body = 'Anbei der Export der Bestellungen vom ' . $datumString;

            $emails = explode(';', $this->exportMailRecipients );

            $output->writeln('File path: ' . $bestellungExportPath);
            foreach ($emails as $email) {
                $output->writeln('Sending email to ' . $email);
                $output->writeln('Email Subject: ' . $emailSubject);
                $this->emailNotificationService->sendEmail($email, $emailSubject, $body, $body, $attachments);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}