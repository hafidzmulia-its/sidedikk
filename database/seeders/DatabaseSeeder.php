<?php

namespace Database\Seeders;

use App\Enums\EducationPostStatus;
use App\Enums\VersionStatus;
use App\Models\EducationPost;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\RiskLevel;
use App\Models\RiskRuleVersion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Pengguna Demo',
            'email' => 'demo@sidedikk.test',
        ]);

        $questionnaireVersion = QuestionnaireVersion::query()->create([
            'version_number' => 1,
            'title' => 'Instrumen SIDEDIKK v1 - ',
            'status' => VersionStatus::Published,
            'published_at' => now(),
            'max_score_snapshot' => 68,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        collect([
            ['Apakah usia Anda kurang dari 20 tahun atau lebih dari 35 tahun?', 2],
            ['Apakah tinggi badan Anda kurang dari 145 cm?', 2],
            ['Apakah Anda pernah melahirkan dengan operasi sesar?', 3],
            ['Apakah Anda pernah mengalami tekanan darah tinggi saat hamil?', 3],
            ['Apakah Anda pernah mengalami perdarahan saat kehamilan sebelumnya?', 3],
            ['Apakah jarak kehamilan Anda kurang dari 2 tahun?', 2],
            ['Apakah Anda memiliki riwayat anemia?', 2],
            ['Apakah Anda memiliki riwayat diabetes atau gula darah tinggi?', 3],
            ['Apakah Anda sering mengalami sakit kepala hebat?', 3],
            ['Apakah penglihatan Anda kabur atau berkunang-kunang?', 3],
            ['Apakah tangan, kaki, atau wajah Anda bengkak mendadak?', 3],
            ['Apakah Anda mengalami nyeri perut hebat?', 4],
            ['Apakah Anda mengalami sesak napas berat?', 4],
            ['Apakah gerakan janin terasa berkurang?', 4],
            ['Apakah Anda mengalami demam tinggi?', 3],
            ['Apakah Anda mengalami mual muntah berlebihan?', 2],
            ['Apakah Anda mengalami perdarahan dari jalan lahir?', 6],
            ['Apakah Anda mengalami keluar air ketuban sebelum waktunya?', 5],
            ['Apakah Anda pernah kejang atau pingsan saat hamil ini?', 6],
            ['Apakah Anda mengalami kontraksi sebelum usia kehamilan 37 minggu?', 5],
        ])->each(function (array $item, int $index) use ($questionnaireVersion): void {
            Question::query()->create([
                'questionnaire_version_id' => $questionnaireVersion->id,
                'text' => $item[0],
                'score_yes' => $item[1],
                'score_no' => 0,
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        });

        $riskRuleVersion = RiskRuleVersion::query()->create([
            'version_number' => 1,
            'title' => 'Aturan Risiko SIDEDIKK v1 - ',
            'status' => VersionStatus::Published,
            'published_at' => now(),
            'max_score_covered' => 68,
            'is_demo_data' => true,
            'medical_approval_required' => true,
        ]);

        collect([
            ['Risiko Rendah', 'rendah', 0, 4, 'success'],
            ['Risiko Sedang', 'sedang', 5, 8, 'warning'],
            ['Risiko Tinggi', 'tinggi', 9, 14, 'danger'],
            ['Risiko Sangat Tinggi', 'sangat-tinggi', 15, 68, 'danger'],
        ])->each(function (array $item, int $index) use ($riskRuleVersion): void {
            RiskLevel::query()->create([
                'risk_rule_version_id' => $riskRuleVersion->id,
                'name' => $item[0],
                'slug' => $item[1],
                'min_score' => $item[2],
                'max_score' => $item[3],
                'semantic_color' => $item[4],
                'description' => '',
                'recommendation' => '',
                'display_priority' => $index + 1,
                'is_active' => true,
            ]);
        });

        collect([
            [
                'title' => 'Kenali Sejak Dini, Cegah Risiko Kehamilan',
                'excerpt' => 'Kenali tanda bahaya kehamilan sejak awal.',
                'body' => "\n\nTanda bahaya yang perlu diwaspadai:\n- Perdarahan pervaginam pada trimester pertama, kedua, atau ketiga.\n- Sakit kepala hebat disertai pandangan kabur atau kejang.\n- Bengkak mendadak pada wajah, tangan, atau kaki.\n- Gerakan janin berkurang atau tidak terasa.\n- Air ketuban keluar sebelum waktunya.\n\nJangan tunda pemeriksaan bila muncul keluhan berat.",
                'cover_image_path' => '/demo/education/artikel-1.png',
            ],
            [
                'title' => 'Faktor Risiko Kehamilan',
                'excerpt' => 'Ringkasan faktor yang perlu diperhatikan selama kehamilan.',
                'body' => "\n\nFaktor yang sering dipantau:\n- Usia ibu kurang dari 20 tahun atau lebih dari 35 tahun.\n- Obesitas atau berat badan kurang.\n- Riwayat anemia atau penyakit kronis.\n- Riwayat kehamilan dengan risiko tinggi.\n- Jarak kehamilan terlalu dekat.\n- Kebiasaan merokok, alkohol, atau narkoba.\n\nLangkah umum:\n- Periksa kehamilan secara rutin.\n- Jalani pola makan bergizi.\n- Istirahat cukup dan kelola stres.\n- Ikuti saran tenaga kesehatan.",
                'cover_image_path' => '/demo/education/artikel-2.png',
            ],
            [
                'title' => 'ANC Rutin, Kehamilan Lebih Aman',
                'excerpt' => 'Pengingat jadwal kunjungan kehamilan berkala.',
                'body' => "\n\nJadwal pemeriksaan ANC:\n- Trimester 1 (0-12 minggu): minimal 1 kali.\n- Trimester 2 (13-28 minggu): minimal 2 kali.\n- Trimester 3 (29-40 minggu): minimal 3 kali.\n\nANC membantu pemantauan tumbuh kembang janin, kondisi ibu, dan persiapan persalinan.\n\nLakukan kunjungan sesuai anjuran tenaga kesehatan.",
                'cover_image_path' => '/demo/education/artikel-3.png',
            ],
        ])->each(function (array $post): void {
            EducationPost::query()->create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'excerpt' => $post['excerpt'],
                'body' => $post['body'],
                'cover_image_path' => $post['cover_image_path'],
                'status' => EducationPostStatus::Published,
                'published_at' => now(),
                'is_demo_data' => true,
            ]);
        });

        $this->call(AdminUserSeeder::class);
    }
}
