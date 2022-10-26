<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use App\Traits\ApiResponser;

class CountController extends Controller
{
    use ApiResponser;
    public function count()
    {
        $users = User::count();
        $services = Service::count();
        $projects = Project::count();
        $news = News::count();

        return $this->successResponse([
            'users' => $users,
            'services' => $services,
            'projects' => $projects,
            'news' => $news
        ]);
    }
}
