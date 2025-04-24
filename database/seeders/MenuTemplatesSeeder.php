<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\MenuTemplate;

class MenuTemplatesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [ 'name'=>'Menu California S', 'price'=>22.00,
              'desc'=>'8 californias au choix, une boisson au choix, un dessert au choix',
              'cats'=>['californias'=>[8,8],'boissons'=>[1,1],'desserts'=>[1,1]], 'img'=>'images/menus/california-s.jpg'],
            [ 'name'=>'Menu California M', 'price'=>27.00,
              'desc'=>'12 californias au choix, une boisson au choix, un dessert au choix',
              'cats'=>['californias'=>[12,12],'boissons'=>[1,1],'desserts'=>[1,1]], 'img'=>'images/menus/california-m.jpg'],
            [ 'name'=>'Menu Maki S', 'price'=>20.00,
              'desc'=>'8 makis au choix, une boisson au choix, un dessert au choix',
              'cats'=>['makis'=>[8,8],'boissons'=>[1,1],'desserts'=>[1,1]], 'img'=>'images/menus/maki-s.jpg'],
            [ 'name'=>'Menu Maki M', 'price'=>23.50,
              'desc'=>'12 makis au choix, une boisson au choix, un dessert au choix',
              'cats'=>['makis'=>[12,12],'boissons'=>[1,1],'desserts'=>[1,1]], 'img'=>'images/menus/maki-m.jpg'],
            [ 'name'=>'Menu Mixte S', 'price'=>32.00,
              'desc'=>'2 nigiris, 4 californias, 4 makis, 1 boisson, 1 dessert',
              'cats'=>['nigiris'=>[2,2],'californias'=>[4,4],'makis'=>[4,4],'boissons'=>[1,1],'desserts'=>[1,1]],
              'img'=>'images/menus/mixte-s.jpg'],
            [ 'name'=>'Menu Mixte M', 'price'=>39.50,
              'desc'=>'3 nigiris, 6 californias, 6 makis, 1 boisson, 1 dessert',
              'cats'=>['nigiris'=>[3,3],'californias'=>[6,6],'makis'=>[6,6],'boissons'=>[1,1],'desserts'=>[1,1]],
              'img'=>'images/menus/mixte-m.jpg'],
        ];

        foreach ($data as $row) {
            $tpl = MenuTemplate::updateOrCreate(
                ['name'=>$row['name']],
                ['description'=>$row['desc'],'price'=>$row['price'],'image'=>$row['img']]
            );

            foreach ($row['cats'] as $slug=>$limits) {
                if ($cat = Category::whereSlug($slug)->first()) {
                    $tpl->categories()->syncWithoutDetaching([
                        $cat->id => ['min_items'=>$limits[0],'max_items'=>$limits[1]]
                    ]);
                }
            }
        }
    }
}
