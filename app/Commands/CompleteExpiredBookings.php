<?php

namespace App\Commands;

use App\Models\BookingModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CompleteExpiredBookings extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Bookings';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'bookings:complete-expired';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Automatically checks approved bookings and marks those past their end time as completed.';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Checking for expired approved bookings...', 'yellow');

        $bookingModel = new BookingModel();
        $completedCount = $bookingModel->completeExpiredBookings();

        if ($completedCount > 0) {
            CLI::write("Successfully marked {$completedCount} expired booking(s) as completed.", 'green');
        } else {
            CLI::write('No expired bookings found. All room schedules up to date.', 'cyan');
        }
    }
}

