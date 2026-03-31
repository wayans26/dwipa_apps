<?php

namespace App\Console\Commands;

use App\Mail\permitReminderMail;
use App\Models\license_permit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendPermitReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permits:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email reminder permit berdasarkan days_before, reminder_interval, dan last_send';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        //
        $now = Carbon::now();

        // Ambil data yang berpotensi dikirim
        // (filter berat dilakukan di PHP karena trigger date per-row)

        license_permit::query()
            ->where('send_notification', 1)                 // (1) hanya yang aktif
            ->whereNotNull('email')                         // (3) kirim ke field email
            ->whereNotNull('permit_end_date')
            ->chunk(500, function ($permits) use ($now) {
                foreach ($permits as $permit) {

                    $end = Carbon::parse($permit->permit_end_date);
                    $emails = array_filter(array_map("trim", explode(";", $permit->email)));
                    $emailValidate = Validator::make([
                        'emails' => $emails
                    ], [
                        'emails'    => 'required|array|min:1',
                        'emails.*'  => 'email:rfc,dns'
                    ], [
                        'emails.*.email'    => 'One or more emails are invalid'
                    ]);

                    if ($emailValidate->fails()) {
                        continue;
                    }

                    // Opsional: kalau sudah lewat end date, tidak kirim
                    if ($now->gt($end)) {
                        continue;
                    }

                    // (2) Trigger pertama: end_date - days_before (unit dari days_before_type)
                    $daysBeforeUnit = $this->toCarbonUnit($permit->days_before_type, 'day');
                    $triggerAt = $end->copy()->sub((int) $permit->days_before, $daysBeforeUnit);

                    // Belum masuk window pengiriman
                    if ($now->lt($triggerAt)) {
                        continue;
                    }

                    // Interval pengingat berikutnya: berdasarkan reminder_interval (unit dari reminder_interval_type)
                    $intervalUnit = $this->toCarbonUnit($permit->reminder_interval_type, 'day');

                    $due = false;
                    if (empty($permit->last_send)) {
                        // Belum pernah kirim, dan sudah masuk window => kirim
                        $due = true;
                    } else {
                        $lastSend = Carbon::parse($permit->last_send);
                        $nextSend = $lastSend->copy()->add((int) $permit->reminder_interval, $intervalUnit);

                        if ($now->gte($nextSend)) {
                            $due = true;
                        }
                    }

                    if (! $due) {
                        continue;
                    }

                    // (4) Queue email
                    foreach ($emails as $key => $value) {
                        Mail::to($value)->queue(new permitReminderMail($permit->id));
                    }

                    // Update penanda last_send
                    $permit->forceFill([
                        'last_send' => $now,
                    ])->save();
                }
            });

        return self::SUCCESS;
    }

    /**
     * Map field type -> Carbon unit.
     * Sesuaikan nilai enum/string di DB kamu.
     */
    private function toCarbonUnit(?string $type, string $default): string
    {
        $t = strtolower(trim((string) $type));

        return match ($t) {
            'minute', 'minutes', 'min' => 'minute',
            'hour', 'hours'            => 'hour',
            'day', 'days'              => 'day',
            'week', 'weeks'            => 'week',
            'month', 'months'          => 'month',
            default                    => $default,
        };
    }
}
