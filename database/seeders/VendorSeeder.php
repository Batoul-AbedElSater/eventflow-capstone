<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder{
    public function run(): void{

    $vendors=[
     [
                'name'        => 'The Flower Shop',
                'category'    => 'decoration',
                'phoneNumber' => '03795481',
                'description' => 'Professional flower decoration for all events.',
                'locations'   => ['Hadi Nasrallah Avenue, Al Afak Institute Bldg., Haret Hreik, Beirut'],
                'instagram'   => 'theflowershoplb',
                'rating'      => 4.8,
                'imageIcon'   => 'images/theflowershop.jpeg',
            ],
            [
                'name'        => 'Cremino',
                'category'    => 'catering',
                'phoneNumber' => '01 453 800',
                'description' => 'High-end dessert shop offering specialty & custom cakes, ice cream & chocolates.',
                'locations'   => [
                    'Roma Street, Centre Miraj Building, Sanayeh, Beirut',
                    'Old Airport Road, Beirut',
                    'Hadath, Baabda, Lebanon',
                    'Saida–Tyre Highway, near Abbasiyeh exit, Tyre',
                ],
                'instagram'   => 'cremino.lb',
                'rating'      => 4.6,
                'imageIcon'   => 'images/cremino.jpeg',
            ],
            [
                'name'        => 'Aljawad Dining',
                'category'    => 'catering',
                'phoneNumber' => '03 456 854',
                'description' => 'Outdoor seating. Great cocktails. Vegan options.',
                'locations'   => [
                    'New Airport Road, beside Golf Club, Beirut',
                    'Centro Mall, Jnah, Beirut',
                    'Ghobeiry, Baabda, Lebanon',
                    'Michel Zakhour Street, Chyah, Baabda',
                    'Saida–Tyre Highway, Abbasiyeh, Tyre',
                ],
                'instagram'   => 'aljawaddining',
                'rating'      => 4.3,
                'imageIcon'   => 'images/jawad.jpeg',
            ],
            [
                'name'        => 'Planto',
                'category'    => 'decoration',
                'phoneNumber' => '03 921 490',
                'description' => 'A modern flower shop in Beirut known for artistic and elegant floral arrangements.',
                'locations'   => ['Hadi Nasrallah Highway, Beirut'],
                'instagram'   => 'plantoleb',
                'rating'      => 2.5,
                'imageIcon'   => 'images/planto.jpeg',
            ],
            [
                'name'        => 'Lancaster Eden Bay',
                'category'    => 'venue',
                'phoneNumber' => '01 838 000',
                'description' => "Beirut's most luxurious ballroom venue, accommodating up to 600 guests for fairytale events.",
                'locations'   => ['El Akhtal El Saghir Street, Ramlet El Baida, Beirut'],
                'instagram'   => 'lancasteredenbay',
                'rating'      => 3.3,
                'imageIcon'   => 'images/lancaster.jpeg',
            ],

    ];
    foreach($vendors as $data){
        Vendor::firstOrCreate(['name'=>$data['name']],$data);
    }
    }
}
