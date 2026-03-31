<?php

use App\Http\Utils\makeid;
use App\Models\department;
use App\Models\menu_group;
use App\Models\menu_sub_group;
use App\Models\role;
use App\Models\user;
use App\Models\user_has_menu_sub_group;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->string('username', 255)->unique();
            $table->string('full_name');
            $table->string('phone', 13);
            $table->string('email', 50);
            $table->string('password');
            $table->string('role_id', 16);
            $table->tinyInteger('status')->default(1);
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });

        $userId = makeid::createId(16);
        $roleId = makeid::createId(16);

        role::create([
            'id'        => $roleId,
            'role_name' => 'ADMINISTRATOR',
        ]);

        $menus = [
            [
                'name'          => 'Settings',
                'order_no'      => 1,
                'icon'          => 'fa fa-gear',
                'sub'           => [
                    [
                        'name'  => 'Role',
                        'page_name' => 'role',
                        'order_no'  => 1,
                    ],
                    [
                        'name'  => 'Menu Editor',
                        'page_name' => 'sub_menu',
                        'order_no'  => 2,
                    ],
                    [
                        'name'  => 'Menu Group',
                        'page_name' => 'menu_group',
                        'order_no'  => 3,
                    ],
                    [
                        'name'  => 'User Manager',
                        'page_name' => 'user_manager',
                        'order_no'  => 4,
                    ]
                ]
            ]
        ];
        $menu_insert = array();
        $sub_menu_insert = array();

        foreach ($menus as $key => $value) {
            $id_menu = makeid::createId(16);
            array_push($menu_insert, [
                'id'        => $id_menu,
                'name'      => $value['name'],
                'order_no'  => $value['order_no'],
                'icon'      => $value['icon']
            ]);
            foreach ($value['sub'] as $key_sub => $value_sub) {
                array_push($sub_menu_insert, [
                    'id'            => makeid::createId(16),
                    'name'          => $value_sub['name'],
                    'page_name'     => $value_sub['page_name'],
                    'order_no'      => $value_sub['order_no'],
                    'menu_group_id' => $id_menu
                ]);
            }
        }

        menu_group::insert($menu_insert);
        menu_sub_group::insert($sub_menu_insert);

        foreach (menu_sub_group::get() as $key => $value) {
            user_has_menu_sub_group::create([
                'role_id'           => $roleId,
                'menu_sub_group_id' => $value->id,
                'allow_view'        => 1,
                'allow_create'      => 1,
                'allow_update'      => 1,
                'allow_delete'      => 1,
                'allow_print'       => 1
            ]);
        }

        user::create([
            'id'            => $userId,
            'username'      => 'admin',
            'full_name'     => 'Admin',
            'phone'         => '-',
            'email'         => 'admin@admin.com',
            'password'      => Hash::make("admin"),
            'role_id'       => $roleId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
