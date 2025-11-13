<?php

namespace App\Console\Commands;
use App\Models\PolicyVersion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class UpdatePolicyVersionCommand extends Command
{
    use SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:politica';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
       
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url="https://cicytex.juntaex.es/web/guest/politica-de-privacidad/";
        // 1. Fetch content
        $response = Http::get($url);
        if (! $response->ok()) {
            Log::error("Failed to fetch policy from {$url}");
            return;
        }
        $html = $response->body();

        // 2. Normalize text
        $text = preg_replace('/\s+/', ' ', strip_tags($html));

        // 3. Compute hash
        $hash = hash('sha256', $text);

        // 4. Get latest record
        /** @var PolicyVersion|null */
        $current = PolicyVersion::orderByDesc('id')->first();

        if (! $current || $current->policy_hash !== $hash) {
            // bump version if exists
            $version = $current
                ? implode('.', array_map('intval', explode('.', $current->policy_version)) + [1])
                : '1.0';

            PolicyVersion::create([
                'policy_url'     => $url,
                'policy_version' => $version,
                'policy_hash'    => $hash,
            ]);

            Log::info("Policy updated to version {$version}");
        } else {
            Log::info('Policy is up to date.');
        }
        return 0;
    }
}
