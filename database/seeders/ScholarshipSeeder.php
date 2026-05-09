<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scholarship;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scholarship::truncate();

        Scholarship::create([
            'title' => 'Post Matric Scholarship for SC/OBC Students',
            'title_hi' => 'एससी/ओबीसी छात्रों के लिए पोस्ट मैट्रिक छात्रवृत्ति',
            'title_pa' => 'ਐਸਸੀ/ਓਬੀਸੀ ਵਿਦਿਆਰਥੀਆਂ ਲਈ ਪੋਸਟ ਮੈਟ੍ਰਿਕ ਸਕਾਲਰਸ਼ਿਪ',
            'description' => 'Financial assistance to SC/OBC students studying at post matriculation or post-secondary stage.',
            'description_hi' => 'मैट्रिकुलेशन या पोस्ट-सेकेंडरी स्तर पर पढ़ने वाले एससी/ओबीसी छात्रों को वित्तीय सहायता।',
            'description_pa' => 'ਮੈਟ੍ਰਿਕ ਜਾਂ ਪੋਸਟ-ਸੈਕੰਡਰੀ ਪੱਧਰ \'ਤੇ ਪੜ੍ਹ ਰਹੇ ਐਸਸੀ/ਓਬੀਸੀ ਵਿਦਿਆਰਥੀਆਂ ਨੂੰ ਵਿੱਤੀ ਸਹਾਇਤਾ।',
            'eligibility_criteria' => 'Family income should be less than Rs. 2.50 Lakh per annum.',
            'eligibility_criteria_hi' => 'पारिवारिक आय प्रति वर्ष 2.50 लाख रुपये से कम होनी चाहिए।',
            'eligibility_criteria_pa' => 'ਪਰਿਵਾਰਕ ਆਮਦਨ ਸਾਲਾਨਾ 2.50 ਲੱਖ ਰੁਪਏ ਤੋਂ ਘੱਟ ਹੋਣੀ ਚਾਹੀਦੀ ਹੈ।',
            'deadline' => now()->addMonths(2),
            'amount' => 'Variable',
            'amount_hi' => 'परिवर्तनीय',
            'amount_pa' => 'ਪਰਿਵਰਤਨਸ਼ੀਲ',
            'url' => 'https://scholarships.punjab.gov.in/',
        ]);

        Scholarship::create([
            'title' => 'National Means cum Merit Scholarship',
            'title_hi' => 'राष्ट्रीय साधन सह योग्यता छात्रवृत्ति (NMMS)',
            'title_pa' => 'ਨੈਸ਼ਨਲ ਮੀਨਜ਼ ਕਮ ਮੈਰਿਟ ਸਕਾਲਰਸ਼ਿਪ',
            'description' => 'Awarded to meritorious students of economically weaker sections to arrest their drop out at class VIII.',
            'description_hi' => 'आर्थिक रूप से कमजोर वर्गों के मेधावी छात्रों को कक्षा VIII में उनकी पढ़ाई छोड़ने से रोकने के लिए प्रदान की जाती है।',
            'description_pa' => 'ਆਰਥਿਕ ਤੌਰ \'ਤੇ ਕਮਜ਼ੋਰ ਵਰਗਾਂ ਦੇ ਹੋਣਹਾਰ ਵਿਦਿਆਰਥੀਆਂ ਨੂੰ ਅੱਠਵੀਂ ਜਮਾਤ ਵਿੱਚ ਪੜ੍ਹਾਈ ਛੱਡਣ ਤੋਂ ਰੋਕਣ ਲਈ ਦਿੱਤੀ ਜਾਂਦੀ ਹੈ।',
            'eligibility_criteria' => 'Students studying in class VIII who have secured minimum 55% marks in class VII.',
            'eligibility_criteria_hi' => 'कक्षा VIII में पढ़ रहे छात्र जिन्होंने कक्षा VII में न्यूनतम 55% अंक प्राप्त किए हैं।',
            'eligibility_criteria_pa' => 'ਅੱਠਵੀਂ ਜਮਾਤ ਵਿੱਚ ਪੜ੍ਹ ਰਹੇ ਵਿਦਿਆਰਥੀ ਜਿਨ੍ਹਾਂ ਨੇ ਸੱਤਵੀਂ ਜਮਾਤ ਵਿੱਚ ਘੱਟੋ-ਘੱਟ 55% ਅੰਕ ਪ੍ਰਾਪਤ ਕੀਤੇ ਹਨ।',
            'deadline' => now()->addMonths(1),
            'amount' => 'Rs. 12,000/- per annum',
            'amount_hi' => '12,000 रुपये प्रति वर्ष',
            'amount_pa' => '12,000 ਰੁਪਏ ਪ੍ਰਤੀ ਸਾਲ',
            'url' => 'https://scholarships.gov.in/',
        ]);
    }
}
