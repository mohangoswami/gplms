<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Category;

class UpdateUserCategoryIds extends Command
{
    protected $signature = 'update:user-category-ids';
    protected $description = 'Update users with category_id based on categories table';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->category) {
                $category = Category::where('name', $user->category)->first();

                if ($category) {
                    $user->update(['category_id' => $category->id]);
                }
            }
        }

        $this->info('User category IDs updated successfully.');
    }
}
