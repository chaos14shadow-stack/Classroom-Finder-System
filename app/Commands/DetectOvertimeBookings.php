<?php

namespace App\Commands;

use App\Models\BookingModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DetectOvertimeBookings extends BaseCommand
{
    protected $group       = 'Bookings';
    protected $name        = 'bookings:detect-overtime';
    protected $description = 'Checks active bookings that have passed their scheduled end time without vacation confirmation and marks them as overtime.';

    public function run(array $params)
    {
        CLI::write('Checking for bookings running overtime...', 'yellow');

        $bookingModel = new BookingModel();
        $results = $bookingModel->detectOvertimeBookings();

        if ($results['overtime_count'] > 0) {
            CLI::write("Detected {$results['overtime_count']} booking(s) currently in OVERTIME.", 'red');
            CLI::write("Flagged {$results['delayed_count']} upcoming booking(s) as potentially delayed.", 'yellow');
        } else {
            CLI::write('All ongoing room bookings are currently running on schedule.', 'green');
        }
    }
}
