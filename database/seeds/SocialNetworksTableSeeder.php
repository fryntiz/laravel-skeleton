<?php

use App\SocialNetwork;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Class SocialNetworksTableSeeder
 *
 * Inserta datos en la tabla de redes sociales con las principales más usadas.
 */
class SocialNetworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'social_networks';

        $timestamp = Carbon::now();

        ## Facebook
        DB::table($table)->insert([
            'name' => 'Facebook',
            'type' => 'facebook',
            'color' => '#3b5998',
            'url' => 'https://www.facebook.com/',
            'icon' => 'fa fa-facebook',
            'image' => 'images/icon/social-network/facebook.png',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        ## Twitter
        DB::table($table)->insert([
            'name' => 'Twitter',
            'type' => 'twitter',
            'color' => '#1DA1F2',
            'url' => 'https://www.twitter.com/',
            'icon' => 'fa fa-twitter',
            'image' => 'images/icon/social-network/twitter.png',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        ## Instagram
        DB::table($table)->insert([
            'name' => 'Instagram',
            'type' => 'instagram',
            'color' => '#bc2a8d',
            'url' => 'https://www.instagram.com/',
            'icon' => 'fa fa-instagram',
            'image' => 'images/icon/social-network/instagram.png',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }
}
