<?php
declare(strict_types = 1);

use app\Entity\Page;
use app\Repository\Page\PageRepository;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(PageRepository $pageRepository): void
    {
        $pageRepository->deleteAll();
        $pageRepository->create(new Page(
            __('seeding.pages.0.title'),
            __('seeding.pages.0.content'),
            'welcome'
        ));
    }
}
