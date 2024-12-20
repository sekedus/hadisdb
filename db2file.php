<?php
$matan_terjemah = [
  'dir' => 'matan-terjemah',
  'kitab' => ['Shahih_Bukhari', 'Shahih_Muslim', 'Sunan_Abu_Daud', 'Sunan_Tirmidzi', 'Sunan_Nasai', 'Sunan_Ibnu_Majah', 'Musnad_Darimi', 'Muwatho_Malik', 'Musnad_Ahmad', 'Sunan_Daraquthni', 'Musnad_Syafii', 'Mustadrak_Hakim', 'Shahih_Ibnu_Khuzaimah', 'Shahih_Ibnu_Hibban', 'Bulughul_Maram', 'Riyadhus_Shalihin']
];

$matan_arab = [
  'dir' => 'matan-arab',
  'kitab' => ['Al_Adabul_Mufrad', 'Mushannaf_Ibnu_Abi_Syaibah', 'Mushannaf_Abdurrazzaq', 'Musnad_Abu_Yala', 'Musnad_Bazzar', 'Mujam_Thabarani_Shaghir', 'Mujam_Thabarani_Awsath', 'Mujam_Thabarani_Kabir', 'Hilyatul_Aulia', 'Doa_Thabarani', 'Arbain_Nawawi_I', 'Arbain_Nawawi_II', 'Akhlak_Rawi_Khatib', 'Mukhtashar_Qiyamullail_Marwazi', 'Syuabul_Iman_Baihaqi', 'Shahih_Ibnu_Khuzaimah_Arab', 'Shahih_Ibnu_Hibban_Arab', 'Riyadhus_Shalihin_Arab', 'Shahih_Adabul_Mufrad_Terjemah', 'Silsilah_Shahihah_Terjemah', 'Bulughul_Maram_Arab', 'Bulughul_Maram_Tahqiq_Fahl', 'Sunan_Baihaqi_Shaghir', 'Sunan_Baihaqi_Kabir', 'Targhib_wat_Tarhib_Mundziri', 'Majmauz_Zawaid']
];

$syarah_arab = [
  'dir' => 'syarah-arab',
  'kitab' => ['Fathul_Bari_Ibnu_Hajar', 'Syarh_Shahih_Muslim_Nawawi', 'Aunul_Mabud', 'Tuhfatul_Ahwadzi', 'Hasyiatus_Sindi_Nasai', 'Hasyiatus_Sindi_Ibnu_Majah', 'Tamhid_Ibnu_Abdil_Barr', 'Mirqatul_Mafatih_Ali_Al_Qari', 'Syarah_Arbain_Nawawi_Ibnu_Daqiq', 'Penjelasan_Hadis_Pilihan', 'Faidhul_Qadir', 'Mustadrak_Hakim_Arab', 'Silsilah_Shahihah_Albani']
];

$harokat = array("َ", "ِ", "ُ", "ً", "ٍ", "ٌ", "ْ", "ّ");

$data = $matan_terjemah; // $matan_terjemah | $matan_arab | $syarah_arab
$api = 'http://api2.carihadis.com';

function _check($str) {
  return $str == '' || $str === '0' ? '' : ' | ' . preg_replace('/^[\s\t]+/', '', $str);
}

foreach ($data['kitab'] as $no => $kitab) {
  if (!is_dir($data['dir'])) mkdir($data['dir'], 0777, true);

  $kitab_file = $data['dir'] . '/' . ($no + 1) . '_' . $kitab;
  if (!file_exists($kitab_file)) file_put_contents($kitab_file, '');

    $id = count(file($kitab_file)) + 1;
    while (true) {
        $url = "$api/?kitab=$kitab&id=$id";
        echo "get: $url \n";
        $get = json_decode(file_get_contents($url), true);
        if (count($get['data']) == 0) break;

        $nass = $get['data']['1']['nass'];
        $nass = preg_replace('/\r\n|\r|\n/', "\\n", $nass);
        // $nass_gundul = str_replace($harokat, '', $nass);
        // $nass_gundul = preg_replace('/\r\n|\r|\n/', "\\n", $nass_gundul);
        $terjemah = $get['data']['1']['terjemah'];
        $terjemah = preg_replace('/\r\n|\r|\n/', "\\n", $terjemah);

        $hadits =  _check($nass) . _check($terjemah);
        file_put_contents($kitab_file, $id . $hadits . "\n", FILE_APPEND | LOCK_EX);
        $id++;
        sleep(1);
    }
}
