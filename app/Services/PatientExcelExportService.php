<?php

namespace App\Services;

use App\Models\HealthMonitoring;
use App\Models\ScreeningIdentity;
use App\Models\ScreeningSession;
use App\Models\SelfManagementLog;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientExcelExportService
{
    /**
     * Download Excel (.xls) spreadsheet with multi-sheet patient recap.
     */
    public function download(): StreamedResponse
    {
        $filename = 'rekap-pasien-nersia-' . now()->format('Ymd-His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Pragma' => 'public',
        ];

        return response()->stream(function () {
            $this->generateXmlSpreadsheet();
        }, 200, $headers);
    }

    /**
     * Output full XML Spreadsheet 2003 format.
     */
    public function generateXmlSpreadsheet(): void
    {
        $sessions = ScreeningSession::with(['user', 'identity'])->latest('id')->get();
        $users = User::withCount(['screeningSessions', 'healthMonitorings', 'screeningIdentities'])->latest('id')->get();
        $monitorings = HealthMonitoring::with('user')->latest('recorded_at')->get();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Nersia.co</Author>
  <Created><?= now()->toIso8601String() ?></Created>
  <Company>Nersia Health</Company>
  <Title>Rekapitulasi Data Pasien, Skrining, dan Self Management</Title>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
  </Style>
  <Style ss:ID="Title">
   <Font ss:FontName="Segoe UI" ss:Size="14" ss:Bold="1" ss:Color="#0F172A"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Subtitle">
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Italic="1" ss:Color="#64748B"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="HeaderPrimary">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HeaderSuccess">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#059669" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HeaderAmber">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#D97706" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="CellNormal">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="CellCenter">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="CellRight">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="RiskHigh">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCA5A5"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCA5A5"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCA5A5"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCA5A5"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Bold="1" ss:Color="#991B1B"/>
   <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="RiskMedium">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCD34D"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCD34D"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCD34D"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FCD34D"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Bold="1" ss:Color="#92400E"/>
   <Interior ss:Color="#FEF3C7" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="RiskLow">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#6EE7B7"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#6EE7B7"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#6EE7B7"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#6EE7B7"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Bold="1" ss:Color="#065F46"/>
   <Interior ss:Color="#D1FAE5" ss:Pattern="Solid"/>
  </Style>
 </Styles>

<?php
        $this->renderSheetRekapSkrining($sessions);
        $this->renderSheetProfilPasien($users);
        $this->renderSheetMonitoringSelfManagement($monitorings);

        echo '</Workbook>';
    }

    /**
     * Sheet 1: Rekap Skrining Lengkap dengan Cell Merging per Pasien
     */
    private function renderSheetRekapSkrining($sessions): void
    {
        // Group by unique patient
        $grouped = $sessions->groupBy(function ($session) {
            $userId = $session->user_id ?? 'guest';
            $name = $session->identity?->name ?? ($session->user?->name ?? 'Pasien');
            $target = $session->identity?->screening_target ?? 'self';
            return "{$userId}_{$name}_{$target}";
        });
        ?>
 <Worksheet ss:Name="Rekap Lengkap Skrining">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="140"/>
   <Column ss:Width="140"/>
   <Column ss:Width="100"/>
   <Column ss:Width="90"/>
   <Column ss:Width="80"/>
   <Column ss:Width="50"/>
   <Column ss:Width="65"/>
   <Column ss:Width="65"/>
   <Column ss:Width="60"/>
   <Column ss:Width="90"/>
   <Column ss:Width="110"/>
   <Column ss:Width="110"/>
   <Column ss:Width="110"/>
   <Column ss:Width="130"/>
   <Column ss:Width="120"/>
   <Column ss:Width="100"/>
   <Column ss:Width="55"/>
   <Column ss:Width="65"/>
   <Column ss:Width="110"/>
   <Column ss:Width="180"/>
   <Column ss:Width="80"/>
   <Column ss:Width="200"/>

   <Row ss:Height="26">
    <Cell ss:MergeAcross="22" ss:StyleID="Title"><Data ss:Type="String">REKAPITULASI DATA SKRINING KESEHATAN LENGKAP - NERSIA.CO</Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="22" ss:StyleID="Subtitle"><Data ss:Type="String">Data Pasien, Pengukuran Fisik (TB, BB, IMT), Hasil Skrining, Skor, dan Tingkat Risiko</Data></Cell>
   </Row>
   <Row><Cell/></Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Nama Akun (User)</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Target Skrining</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Tgl Lahir</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Usia</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">TB (cm)</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">BB (kg)</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">IMT/BMI</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Status Gizi</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Provinsi</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Kabupaten/Kota</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Kecamatan</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Jenis Skrining</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Tgl Skrining</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Tingkat Risiko</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Skor</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Skor Max</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Kategori Hasil</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Rekomendasi / Tindak Lanjut</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Darurat?</Data></Cell>
    <Cell ss:StyleID="HeaderPrimary"><Data ss:Type="String">Ringkasan</Data></Cell>
   </Row>
<?php
        $patientNo = 1;
        foreach ($grouped as $patientKey => $patientSessions) {
            $totalInGroup = $patientSessions->count();
            $mergeAttr = $totalInGroup > 1 ? ' ss:MergeDown="' . ($totalInGroup - 1) . '"' : '';

            $firstSession = $patientSessions->first();
            $user = $firstSession->user;
            $identity = $firstSession->identity;

            $userName = $user?->name ?? 'Tamu / Tanpa Akun';
            $patientName = $identity?->name ?? ($user?->name ?? 'Pasien');
            $target = $identity ? ($identity->screening_target === 'self' ? 'Diri Sendiri' : 'Orang Lain') : 'Diri Sendiri';
            $gender = $identity?->gender ? ucfirst($identity->gender) : ($user?->genderLabel() ?? '—');
            $dob = $identity?->date_of_birth?->format('d/m/Y') ?? ($user?->date_of_birth?->format('d/m/Y') ?? '—');
            $age = $identity?->age ?? ($user?->age ?? '—');
            
            $tb = $identity?->height_cm ?? ($user?->height ? (float)$user->height : null);
            $bb = $identity?->weight_kg ? (float)$identity->weight_kg : ($user?->weight ? (float)$user->weight : null);
            
            $bmi = null;
            $statusGizi = '—';
            if ($tb && $bb && $tb > 0) {
                $hM = $tb / 100;
                $bmi = round($bb / ($hM * $hM), 1);
                if ($bmi < 18.5) {
                    $statusGizi = 'Kurus';
                } elseif ($bmi < 25) {
                    $statusGizi = 'Normal';
                } elseif ($bmi < 30) {
                    $statusGizi = 'Kelebihan BB';
                } else {
                    $statusGizi = 'Obesitas';
                }
            }

            $province = $identity?->province ?? '—';
            $regency = $identity?->regency ?? '—';
            $district = $identity?->district ?? '—';

            foreach ($patientSessions->values() as $idx => $session) {
                $disease = $session->diseaseLabel() ?? ($session->disease ?? 'Skrining');
                $sessionDate = $session->formattedDateTime('d/m/Y H:i');
                
                $riskStyle = 'CellCenter';
                $riskLevel = $session->displayRiskLevel();
                $riskLabel = $session->displayRiskLabel();
                
                if ($riskLevel === 'high' || $session->showsEmergencyUi()) {
                    $riskStyle = 'RiskHigh';
                } elseif ($riskLevel === 'medium') {
                    $riskStyle = 'RiskMedium';
                } elseif ($riskLevel === 'low') {
                    $riskStyle = 'RiskLow';
                }

                $scoreData = $session->scoreData();
                $score = $scoreData['total'] ?? '—';
                $maxScore = $scoreData['max'] ?? '—';
                $scoreCat = $scoreData['hasil_kategori'] ?? ($scoreData['risiko_label'] ?? $session->scoreLabel());
                $recommendation = $session->nextStepMessage();
                $emergency = $session->showsEmergencyUi() ? 'Ya (DARURAT)' : 'Tidak';
                $summary = $session->summary ?? '—';
                ?>
   <Row ss:Height="22">
<?php if ($idx === 0): ?>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="Number"><?= $patientNo ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($userName) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($patientName) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($target) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($gender) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($dob) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="<?= is_numeric($age) ? 'Number' : 'String' ?>"><?= htmlspecialchars((string)$age) ?></Data></Cell>
    <Cell ss:StyleID="CellRight"<?= $mergeAttr ?>><Data ss:Type="<?= is_numeric($tb) ? 'Number' : 'String' ?>"><?= $tb !== null ? $tb : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"<?= $mergeAttr ?>><Data ss:Type="<?= is_numeric($bb) ? 'Number' : 'String' ?>"><?= $bb !== null ? $bb : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"<?= $mergeAttr ?>><Data ss:Type="<?= is_numeric($bmi) ? 'Number' : 'String' ?>"><?= $bmi !== null ? $bmi : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($statusGizi) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($province) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($regency) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"<?= $mergeAttr ?>><Data ss:Type="String"><?= htmlspecialchars($district) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($disease) ?></Data></Cell>
<?php else: ?>
    <Cell ss:Index="15" ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($disease) ?></Data></Cell>
<?php endif; ?>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($sessionDate) ?></Data></Cell>
    <Cell ss:StyleID="<?= $riskStyle ?>"><Data ss:Type="String"><?= htmlspecialchars($riskLabel) ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($score) ? 'Number' : 'String' ?>"><?= htmlspecialchars((string)$score) ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($maxScore) ? 'Number' : 'String' ?>"><?= htmlspecialchars((string)$maxScore) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars((string)$scoreCat) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($recommendation) ?></Data></Cell>
    <Cell ss:StyleID="<?= $emergency === 'Ya (DARURAT)' ? 'RiskHigh' : 'CellCenter' ?>"><Data ss:Type="String"><?= htmlspecialchars($emergency) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($summary) ?></Data></Cell>
   </Row>
<?php
            }
            $patientNo++;
        }
        ?>
  </Table>
 </Worksheet>
<?php
    }

    /**
     * Sheet 2: Data Profil Pasien Terdaftar
     */
    private function renderSheetProfilPasien($users): void
    {
        ?>
 <Worksheet ss:Name="Daftar Pasien (Akun)">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="150"/>
   <Column ss:Width="160"/>
   <Column ss:Width="110"/>
   <Column ss:Width="90"/>
   <Column ss:Width="80"/>
   <Column ss:Width="50"/>
   <Column ss:Width="65"/>
   <Column ss:Width="65"/>
   <Column ss:Width="60"/>
   <Column ss:Width="120"/>
   <Column ss:Width="180"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="90"/>

   <Row ss:Height="26">
    <Cell ss:MergeAcross="15" ss:StyleID="Title"><Data ss:Type="String">PROFIL PENGGUNA DAN PASIEN TERDAFTAR</Data></Cell>
   </Row>
   <Row><Cell/></Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Nama Lengkap</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Email</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Nomor HP</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Tgl Lahir</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Usia</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">TB (cm)</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">BB (kg)</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">BMI</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Pekerjaan</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Alamat Domisili</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Jml Identitas</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Jml Skrining</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Jml Monitor</Data></Cell>
    <Cell ss:StyleID="HeaderSuccess"><Data ss:Type="String">Tgl Daftar</Data></Cell>
   </Row>
<?php
        $no = 1;
        foreach ($users as $user) {
            $bmi = null;
            if ($user->weight && $user->height && $user->height > 0) {
                $hM = (float)$user->height / 100;
                $bmi = round((float)$user->weight / ($hM * $hM), 1);
            }
            ?>
   <Row ss:Height="22">
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($user->name) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($user->email) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($user->phone ?? '—') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($user->genderLabel() ?? '—') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($user->date_of_birth?->format('d/m/Y') ?? '—') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="<?= is_numeric($user->age) ? 'Number' : 'String' ?>"><?= htmlspecialchars((string)($user->age ?? '—')) ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($user->height) ? 'Number' : 'String' ?>"><?= $user->height ? (float)$user->height : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($user->weight) ? 'Number' : 'String' ?>"><?= $user->weight ? (float)$user->weight : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($bmi) ? 'Number' : 'String' ?>"><?= $bmi !== null ? $bmi : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($user->occupation ?? '—') ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($user->address ?? '—') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $user->screening_identities_count ?? $user->screeningIdentities()->count() ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $user->screening_sessions_count ?? $user->screeningSessions()->count() ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $user->health_monitorings_count ?? $user->healthMonitorings()->count() ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $user->created_at->format('d/m/Y') ?></Data></Cell>
   </Row>
<?php
        }
        ?>
  </Table>
 </Worksheet>
<?php
    }

    /**
     * Sheet 3: Monitoring Kesehatan & Self-Management
     */
    private function renderSheetMonitoringSelfManagement($monitorings): void
    {
        ?>
 <Worksheet ss:Name="Monitoring &amp; Self-Management">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="140"/>
   <Column ss:Width="120"/>
   <Column ss:Width="70"/>
   <Column ss:Width="85"/>
   <Column ss:Width="85"/>
   <Column ss:Width="65"/>
   <Column ss:Width="65"/>
   <Column ss:Width="70"/>
   <Column ss:Width="65"/>
   <Column ss:Width="65"/>
   <Column ss:Width="100"/>
   <Column ss:Width="85"/>
   <Column ss:Width="110"/>
   <Column ss:Width="180"/>

   <Row ss:Height="26">
    <Cell ss:MergeAcross="14" ss:StyleID="Title"><Data ss:Type="String">MONITORING KESEHATAN &amp; LOG SELF-MANAGEMENT</Data></Cell>
   </Row>
   <Row><Cell/></Row>

   <Row ss:Height="28">
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Penyakit</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Tipe</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Tgl Rekam</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Tekanan Darah</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Nadi (bpm)</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Suhu (°C)</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">GDS (mg/dL)</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">BB (kg)</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">SpO2 (%)</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Kepatuhan Obat</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Skor Keluhan</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Self-Management</Data></Cell>
    <Cell ss:StyleID="HeaderAmber"><Data ss:Type="String">Catatan / Evaluasi</Data></Cell>
   </Row>
<?php
        $no = 1;
        foreach ($monitorings as $m) {
            $user = $m->user;
            $bp = ($m->systolic && $m->diastolic) ? "{$m->systolic}/{$m->diastolic} mmHg" : '—';
            $medCompliance = $m->displayMedicationComplianceLabel() ?? ($m->medication_compliance_percent !== null ? "{$m->medication_compliance_percent}%" : '—');
            $selfMgmt = $m->displaySelfManagementLabel() ?? ($m->self_management_percent !== null ? "{$m->self_management_percent}%" : '—');
            $complaint = $m->displayComplaintLabel() ?? ($m->complaint_total !== null ? (string)$m->complaint_total : '—');
            ?>
   <Row ss:Height="22">
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($user?->name ?? 'Pasien') ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($m->diseaseLabel() ?? ($m->disease ?? '—')) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($m->monitorTypeLabel()) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $m->activityDate()->format('d/m/Y') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($bp) ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($m->heart_rate) ? 'Number' : 'String' ?>"><?= $m->heart_rate ?? '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($m->temperature) ? 'Number' : 'String' ?>"><?= $m->temperature ? (float)$m->temperature : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($m->blood_sugar) ? 'Number' : 'String' ?>"><?= $m->blood_sugar ? (float)$m->blood_sugar : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($m->weight) ? 'Number' : 'String' ?>"><?= $m->weight ? (float)$m->weight : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellRight"><Data ss:Type="<?= is_numeric($m->oxygen_saturation) ? 'Number' : 'String' ?>"><?= $m->oxygen_saturation ? (float)$m->oxygen_saturation : '—' ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($medCompliance) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($complaint) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= htmlspecialchars($selfMgmt) ?></Data></Cell>
    <Cell ss:StyleID="CellNormal"><Data ss:Type="String"><?= htmlspecialchars($m->notes ?? '—') ?></Data></Cell>
   </Row>
<?php
        }
        ?>
  </Table>
 </Worksheet>
<?php
    }
}
