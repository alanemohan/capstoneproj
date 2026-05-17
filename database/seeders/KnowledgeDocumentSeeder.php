<?php

namespace Database\Seeders;

use App\Models\KnowledgeDocument;
use App\Services\AI\VectorSearchService;
use Illuminate\Database\Seeder;

class KnowledgeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            // ═══════ MATHEMATICS ═══════
            [
                'title'    => 'Pythagoras Theorem',
                'category' => 'mathematics',
                'keywords' => 'pythagoras,pythagorean,theorem,hypotenuse,right triangle,right angle',
                'content'  => "Pythagoras Theorem states that in a right-angled triangle, the square of the hypotenuse equals the sum of squares of the other two sides: a² + b² = c². Where c is the hypotenuse (longest side opposite the right angle), a and b are the other sides. Example: If a=3, b=4, then c = √(9+16) = √25 = 5. This is called a 3-4-5 Pythagorean Triple. Other common triples: 5-12-13, 8-15-17, 7-24-25.",
            ],
            [
                'title'    => 'Algebra and Linear Equations',
                'category' => 'mathematics',
                'keywords' => 'algebra,equation,variable,linear,solve,expression,quadratic',
                'content'  => "Algebra uses letters (variables) to represent unknown numbers. A linear equation has the form ax + b = c. To solve: isolate the variable. Example: 2x + 3 = 11 → 2x = 8 → x = 4. Key identities: (a+b)² = a² + 2ab + b², (a-b)² = a² - 2ab + b², (a+b)(a-b) = a² - b². Quadratic equation: ax² + bx + c = 0, solved by x = (-b ± √(b²-4ac)) / 2a.",
            ],
            [
                'title'    => 'Fractions, HCF and LCM',
                'category' => 'mathematics',
                'keywords' => 'fraction,numerator,denominator,hcf,lcm,gcd,common factor,common multiple',
                'content'  => "A fraction represents part of a whole: Numerator/Denominator. Types: Proper (3/4), Improper (5/3), Mixed (1½). HCF (Highest Common Factor): largest number dividing two numbers exactly. LCM (Lowest Common Multiple): smallest number divisible by both. HCF × LCM = Product of numbers. Prime factorization method: 12=2²×3, 18=2×3², HCF=2×3=6, LCM=2²×3²=36.",
            ],
            [
                'title'    => 'Geometry: Areas and Volumes',
                'category' => 'mathematics',
                'keywords' => 'area,perimeter,circle,triangle,rectangle,geometry,volume,surface area,circumference',
                'content'  => "2D Shapes: Rectangle: Area=l×b, Perimeter=2(l+b). Square: Area=a², Perimeter=4a. Triangle: Area=½×base×height. Circle: Area=πr², Circumference=2πr. 3D Shapes: Cube: Volume=a³, SA=6a². Cuboid: Volume=l×b×h, SA=2(lb+bh+lh). Cylinder: Volume=πr²h, CSA=2πrh. Sphere: Volume=4/3πr³, SA=4πr².",
            ],
            [
                'title'    => 'Percentages, Profit and Loss',
                'category' => 'mathematics',
                'keywords' => 'percentage,percent,profit,loss,discount,interest,simple interest,compound interest',
                'content'  => "Percentage means per hundred. Formula: (Part/Whole)×100. Profit% = (Profit/CP)×100. Loss% = (Loss/CP)×100. SP = CP×(100+Profit%)/100. Simple Interest: SI = P×R×T/100. Compound Interest: A = P(1+R/100)^T. Discount = Marked Price - Selling Price. Discount% = (Discount/MP)×100.",
            ],
            [
                'title'    => 'Number System',
                'category' => 'mathematics',
                'keywords' => 'natural number,whole number,integer,rational,irrational,real number,prime,composite',
                'content'  => "Natural Numbers (N): 1,2,3... Whole Numbers (W): 0,1,2,3... Integers (Z): ...-2,-1,0,1,2... Rational Numbers: p/q form (e.g., ½, 0.75). Irrational Numbers: cannot be p/q (√2, π). Real Numbers: all rational + irrational. Prime Numbers: divisible only by 1 and themselves (2,3,5,7,11...). Note: 1 is neither prime nor composite, 2 is the only even prime.",
            ],

            // ═══════ SCIENCE ═══════
            [
                'title'    => 'Newton\'s Laws of Motion and Gravity',
                'category' => 'science',
                'keywords' => 'newton,gravity,law of motion,inertia,force,momentum,action reaction,f=ma',
                'content'  => "Newton's Three Laws: 1st Law (Inertia): Object stays at rest unless force acts on it. 2nd Law: F=ma (Force = Mass × Acceleration). 3rd Law: Every action has equal and opposite reaction. Gravity: Force of attraction between objects with mass. g=9.8 m/s² on Earth. Newton saw apple fall and realized same force keeps Moon in orbit. Weight = mass × g. Universal Law: F = GMm/r².",
            ],
            [
                'title'    => 'Photosynthesis',
                'category' => 'science',
                'keywords' => 'photosynthesis,chlorophyll,sunlight,carbon dioxide,glucose,oxygen,plant food,stomata',
                'content'  => "Photosynthesis: process by which green plants make food using sunlight. Equation: 6CO₂ + 6H₂O + Light → C₆H₁₂O₆ + 6O₂. Requirements: Chlorophyll (green pigment), Sunlight (energy), CO₂ (enters through stomata), Water (absorbed by roots). Products: Glucose (stored as starch), Oxygen (released). Location: Chloroplasts in leaf cells. Plants are 'Producers' in food chains.",
            ],
            [
                'title'    => 'Cell Biology',
                'category' => 'science',
                'keywords' => 'cell,nucleus,mitochondria,membrane,organelle,plant cell,animal cell,dna,chromosome',
                'content'  => "Cell: basic unit of life. Key organelles: Nucleus (control center, DNA), Mitochondria (powerhouse, ATP), Cell Membrane (controls entry/exit), Cytoplasm (jelly-like fluid), Ribosomes (protein synthesis), Endoplasmic Reticulum (transport), Golgi Body (packaging). Plant cell extras: Cell Wall (cellulose), Chloroplasts (photosynthesis), Large Vacuole (water storage). Smallest cell: Mycoplasma. Largest: Ostrich egg.",
            ],
            [
                'title'    => 'Periodic Table and Elements',
                'category' => 'science',
                'keywords' => 'periodic table,element,atomic number,valency,metal,nonmetal,noble gas,alkali',
                'content'  => "Periodic Table: 118 elements arranged by atomic number. 18 Groups (columns), 7 Periods (rows). Group 1: Alkali Metals (Li,Na,K) - very reactive. Group 17: Halogens (F,Cl,Br) - reactive non-metals. Group 18: Noble Gases (He,Ne,Ar) - stable/inert. First 10: H,He,Li,Be,B,C,N,O,F,Ne. Metals: left side, conduct electricity. Non-metals: right side. Metalloids: borderline (Si,Ge).",
            ],
            [
                'title'    => 'Light, Reflection and Refraction',
                'category' => 'science',
                'keywords' => 'light,reflection,refraction,lens,mirror,convex,concave,prism,spectrum,rainbow',
                'content'  => "Reflection: light bouncing off surface. Laws: angle of incidence = angle of reflection. Concave mirror: converges light (headlights, telescopes). Convex mirror: diverges light (rear-view mirrors). Refraction: bending of light between media. Convex lens: converges (magnifying glass). Concave lens: diverges (spectacles for myopia). Dispersion: white light → VIBGYOR through prism. Rainbow: dispersion through raindrops.",
            ],

            // ═══════ ENGLISH ═══════
            [
                'title'    => 'English Tenses',
                'category' => 'english',
                'keywords' => 'tense,tenses,past tense,present tense,future tense,verb form,grammar,continuous,perfect',
                'content'  => "Present: Simple (I eat), Continuous (I am eating), Perfect (I have eaten), Perfect Continuous (I have been eating). Past: Simple (I ate), Continuous (I was eating), Perfect (I had eaten). Future: Simple (I will eat), Continuous (I will be eating), Perfect (I will have eaten). Helper verbs: Present→am/is/are, Past→was/were/had, Future→will/shall.",
            ],
            [
                'title'    => 'Parts of Speech',
                'category' => 'english',
                'keywords' => 'noun,pronoun,verb,adjective,adverb,conjunction,preposition,interjection,parts of speech',
                'content'  => "8 Parts of Speech: 1. Noun: name of person/place/thing (Nabha, book). 2. Pronoun: replaces noun (I, you, he, she). 3. Verb: action/state (run, is, think). 4. Adjective: describes noun (tall, beautiful). 5. Adverb: modifies verb (quickly, very). 6. Preposition: shows relationship (in, on, at). 7. Conjunction: joins clauses (and, but, because). 8. Interjection: emotion (Oh!, Wow!).",
            ],

            // ═══════ SOCIAL STUDIES ═══════
            [
                'title'    => 'Indian Independence 1947',
                'category' => 'social_studies',
                'keywords' => 'independence,freedom,british,gandhi,1947,partition,india,republic day,constitution',
                'content'  => "Indian Independence: August 15, 1947. First PM: Jawaharlal Nehru. National Anthem: Jana Gana Mana (Rabindranath Tagore). Key freedom fighters: Mahatma Gandhi (Father of Nation, non-violence), Bhagat Singh (revolutionary, Punjab), Subhas Chandra Bose (Netaji, INA), Sardar Patel (Iron Man), B.R. Ambedkar (Father of Constitution). Events: 1857 First War, 1919 Jallianwala Bagh, 1930 Dandi March, 1942 Quit India, 1947 Independence & Partition. Republic Day: January 26, 1950.",
            ],
            [
                'title'    => 'Indian Constitution',
                'category' => 'social_studies',
                'keywords' => 'constitution,fundamental rights,directive principles,parliament,preamble,lok sabha,rajya sabha',
                'content'  => "Indian Constitution adopted January 26, 1950. Father: Dr. B.R. Ambedkar. Preamble: Sovereign, Socialist, Secular, Democratic Republic. 6 Fundamental Rights: Right to Equality (Art 14-18), Freedom (Art 19-22), Against Exploitation (Art 23-24), Freedom of Religion (Art 25-28), Cultural & Educational (Art 29-30), Constitutional Remedies (Art 32). Parliament: Lok Sabha (Lower House) + Rajya Sabha (Upper House). Longest written constitution in the world.",
            ],
            [
                'title'    => 'Punjab State Facts',
                'category' => 'social_studies',
                'keywords' => 'punjab,nabha,chandigarh,ludhiana,amritsar,patiala,golden temple,granary of india',
                'content'  => "Punjab: Capital Chandigarh (shared with Haryana). Language: Punjabi. Formation: November 1, 1966. Cities: Amritsar (Golden Temple), Ludhiana (industrial capital), Patiala (royal city), Nabha (education hub in Patiala district). Rivers: Sutlej, Beas, Ravi. Agriculture: Granary of India — wheat, rice, sugarcane. Green Revolution started here. Famous for: Bhangra, Giddha, Butter Chicken, Lassi.",
            ],

            // ═══════ COMPUTER SCIENCE ═══════
            [
                'title'    => 'Object-Oriented Programming (OOP)',
                'category' => 'computer_science',
                'keywords' => 'oop,class,object,inheritance,polymorphism,encapsulation,abstraction,java,python,programming',
                'content'  => "OOP Concepts: 1. Class: blueprint for objects. 2. Object: instance of a class. 3. Inheritance: child class inherits from parent (code reuse). 4. Polymorphism: same method behaves differently (method overloading/overriding). 5. Encapsulation: wrapping data and methods, hiding internals (private/public). 6. Abstraction: showing essential features, hiding complexity. Example in Java: class Animal { void sound() {} } class Dog extends Animal { void sound() { System.out.println(\"Bark\"); } }.",
            ],
            [
                'title'    => 'Database Management Systems (DBMS)',
                'category' => 'computer_science',
                'keywords' => 'dbms,database,sql,normalization,table,query,relational,primary key,foreign key,join',
                'content'  => "DBMS: software for managing databases. Types: Relational (MySQL, PostgreSQL), NoSQL (MongoDB). SQL operations: SELECT, INSERT, UPDATE, DELETE. Normalization: 1NF (atomic values), 2NF (no partial dependencies), 3NF (no transitive dependencies), BCNF. Keys: Primary Key (unique identifier), Foreign Key (references another table), Candidate Key, Composite Key. Joins: INNER JOIN, LEFT JOIN, RIGHT JOIN, FULL JOIN. ACID properties: Atomicity, Consistency, Isolation, Durability.",
            ],
            [
                'title'    => 'Machine Learning Basics',
                'category' => 'computer_science',
                'keywords' => 'machine learning,ai,artificial intelligence,neural network,deep learning,classification,regression',
                'content'  => "Machine Learning: subset of AI where computers learn from data without explicit programming. Types: 1. Supervised Learning: labeled data (classification, regression). 2. Unsupervised Learning: unlabeled data (clustering, dimensionality reduction). 3. Reinforcement Learning: reward-based learning. Common algorithms: Linear Regression, Decision Trees, Random Forest, SVM, k-NN, Neural Networks. Deep Learning: multi-layer neural networks for complex patterns (image recognition, NLP).",
            ],
            [
                'title'    => 'Data Structures and Algorithms',
                'category' => 'computer_science',
                'keywords' => 'array,linked list,stack,queue,tree,graph,sorting,searching,algorithm,data structure,binary search',
                'content'  => "Data Structures: Array (contiguous memory, O(1) access), Linked List (nodes with pointers), Stack (LIFO - Last In First Out), Queue (FIFO - First In First Out), Tree (hierarchical, binary tree), Graph (nodes + edges), Hash Table (key-value, O(1) lookup). Sorting: Bubble Sort O(n²), Merge Sort O(n log n), Quick Sort O(n log n avg). Searching: Linear O(n), Binary Search O(log n) on sorted arrays.",
            ],

            // ═══════ LMS PLATFORM ═══════
            [
                'title'    => 'How to Upload a Course',
                'category' => 'lms',
                'keywords' => 'upload course,create course,add course,new course,course upload,teacher dashboard',
                'content'  => "To upload a course: 1. Login to Teacher Dashboard. 2. Click 'My Courses' in sidebar. 3. Click 'Create New Course'. 4. Fill in Title, Subject, Class Level, Language, Description, Price. 5. Upload Thumbnail (max 5MB, JPG/PNG). 6. Click 'Create Course'. 7. Add lessons with 'Add Lesson' button. 8. Submit for admin review. Supported files: PDF, MP4, WebM, MOV, JPG, PNG (max 100MB per file).",
            ],
            [
                'title'    => 'Student Enrollment Process',
                'category' => 'lms',
                'keywords' => 'enroll,enrollment,join course,register course,buy course,free course,access course',
                'content'  => "For Free Courses: Go to Courses → Browse → Click course → Click 'Enroll Now' → Instant access. For Paid Courses: Browse → 'Add to Cart' → 'My Cart' → 'Checkout' → Complete payment → Immediate access. View enrolled courses: Sidebar → 'My Courses'. Track progress with lesson progress bars.",
            ],
            [
                'title'    => 'Password Reset and Login Help',
                'category' => 'lms',
                'keywords' => 'forgot password,reset password,change password,login issue,cant login,otp login',
                'content'  => "Reset via OTP: Login page → 'Forgot Password / Login with OTP' → Enter phone → Enter 6-digit OTP → Logged in → Profile → Change Password. Reset via Profile: Profile page → 'Change Password' → Enter current + new password → 'Update Password'. Login issues: Check email/password, try OTP login, clear browser cache, contact admin if account suspended.",
            ],
            [
                'title'    => 'Teacher Account Approval',
                'category' => 'lms',
                'keywords' => 'approval,approve,pending,teacher pending,account pending,rejected,teacher status',
                'content'  => "Teacher account statuses: Pending (admin hasn't reviewed), Approved (can create courses), Rejected (contact admin). After registration, account is pending until admin reviews. Cannot access teacher dashboard until approved. Notifications sent on approval/rejection. Contact admin if waiting more than 24 hours.",
            ],
        ];

        foreach ($documents as $doc) {
            KnowledgeDocument::updateOrCreate(
                ['title' => $doc['title']],
                $doc
            );
        }

        // Build TF-IDF vectors for all documents
        $vectorService = new VectorSearchService();
        $count = $vectorService->reindexAll();

        $this->command?->info("Seeded and indexed {$count} knowledge documents.");
    }
}
