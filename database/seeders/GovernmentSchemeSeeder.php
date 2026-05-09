<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GovernmentScheme;

class GovernmentSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GovernmentScheme::truncate();

        GovernmentScheme::create([
            'title' => 'Digital India Programme',
            'title_hi' => 'डिजिटल इंडिया कार्यक्रम',
            'title_pa' => 'ਡਿਜੀਟਲ ਇੰਡੀਆ ਪ੍ਰੋਗਰਾਮ',
            'description' => 'A flagship programme to transform India into a digitally empowered society and knowledge economy.',
            'description_hi' => 'भारत को डिजिटल रूप से सशक्त समाज और ज्ञान अर्थव्यवस्था में बदलने के लिए एक प्रमुख कार्यक्रम।',
            'description_pa' => 'ਭਾਰਤ ਨੂੰ ਡਿਜੀਟਲ ਤੌਰ \'ਤੇ ਸਸ਼ਕਤ ਸਮਾਜ ਅਤੇ ਗਿਆਨ ਦੀ ਆਰਥਿਕਤਾ ਵਿੱਚ ਬਦਲਣ ਲਈ ਇੱਕ ਪ੍ਰਮੁੱਖ ਪ੍ਰੋਗਰਾਮ।',
            'target_audience' => 'All Citizens, Students',
            'target_audience_hi' => 'सभी नागरिक, छात्र',
            'target_audience_pa' => 'ਸਾਰੇ ਨਾਗਰਿਕ, ਵਿਦਿਆਰਥੀ',
            'benefits' => 'Access to digital resources, e-governance, digital literacy.',
            'benefits_hi' => 'डिजिटल संसाधनों तक पहुंच, ई-गवर्नेंस, डिजिटल साक्षरता।',
            'benefits_pa' => 'ਡਿਜੀਟਲ ਸਰੋਤਾਂ ਤੱਕ ਪਹੁੰਚ, ਈ-ਗਵਰਨੈਂਸ, ਡਿਜੀਟਲ ਸਾਖਰਤਾ।',
            'url' => 'https://www.digitalindia.gov.in/',
        ]);

        GovernmentScheme::create([
            'title' => 'Beti Bachao Beti Padhao',
            'title_hi' => 'बेटी बचाओ बेटी पढ़ाओ',
            'title_pa' => 'ਬੇਟੀ ਬਚਾਓ ਬੇਟੀ ਪੜ੍ਹਾਓ',
            'description' => 'Aims to generate awareness and improve the efficiency of welfare services intended for girls in India.',
            'description_hi' => 'इसका उद्देश्य जागरूकता पैदा करना और भारत में लड़कियों के लिए इच्छित कल्याण सेवाओं की दक्षता में सुधार करना है।',
            'description_pa' => 'ਇਸਦਾ ਉਦੇਸ਼ ਜਾਗਰੂਕਤਾ ਪੈਦਾ ਕਰਨਾ ਅਤੇ ਭਾਰਤ ਵਿੱਚ ਕੁੜੀਆਂ ਲਈ ਕਲਿਆਣਕਾਰੀ ਸੇਵਾਵਾਂ ਦੀ ਕੁਸ਼ਲਤਾ ਵਿੱਚ ਸੁਧਾਰ ਕਰਨਾ ਹੈ।',
            'target_audience' => 'Girl Students',
            'target_audience_hi' => 'छात्राएं',
            'target_audience_pa' => 'ਲੜਕੀਆਂ (ਵਿਦਿਆਰਥਣਾਂ)',
            'benefits' => 'Educational and financial empowerment for girl children.',
            'benefits_hi' => 'बालिकाओं के लिए शैक्षिक और वित्तीय सशक्तिकरण।',
            'benefits_pa' => 'ਬਾਲੜੀਆਂ ਲਈ ਵਿਦਿਅਕ ਅਤੇ ਵਿੱਤੀ ਸਸ਼ਕਤੀਕਰਨ।',
            'url' => 'https://wcd.nic.in/bbbp-schemes',
        ]);
    }
}
