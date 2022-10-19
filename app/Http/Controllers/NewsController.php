<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;

use App\Models\News;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\News\PutRequest;
use App\Http\Requests\News\StoreRequest;
use App\Http\Requests\DirectionRequest;

use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;

class NewsController extends Controller
{
    use ApiResponser, File, Image;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(IndexRequest $request)
    {
        $newsQuery = News::where('visible', '=', 1)->orderByDesc('position');
        $pageSize = $request->has('page_size') ? $request->page_size : $request::MAX_PAGE_SIZE;

        $result = $newsQuery->paginate($pageSize)->through(function($n){
            return [
                'id' => $n->id,
                'title' => $n->title,
                'content' => $n->content,
                'link' => $n->link,
                'slug' => $n->slug,
                'image' => $n->image,
                'visible' => $n->visible,
            ];
        });

        return $this->successResponse($result);
    }

    public function view($id)
    {
        $news = News::find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }
        return $this->successResponse($this->jsonResponse($news));
    }

    public function store(StoreRequest $request)
    {
        $news = new News();
        $news->title = $request->validated('title');
        $news->content = $request->validated('content');
        $news->link = $request->validated('link');
        $news->slug = $request->validated('slug');
        $news->visible = $request->validated('visible');
        $news->position = $this->getMaxPosition()+1;
        if($request->has('image'))
        {
            try
            {
                $uniqueImgName = $this->generateFileUniqueName(News::class, 'image');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('NEWS_IMAGES'), $uniqueImgName, $imgExtension,);
                $news->image = $uniqueImgName.'.'.$imgExtension;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '.$e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        }
        $news->save();
        return $this->successResponse($this->jsonResponse($news));
    }

    public function update(PutRequest $request, $id)
    {
        $news = News::find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }

        if(!is_null($request->image))
        {
            try
            {
                $uniqueImgName = $this->generateFileUniqueName(News::class, 'image');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('NEWS_IMAGES'), $uniqueImgName, $imgExtension);
                $this->deleteImages(env('NEWS_IMAGES'), $news->image);
                $news->image = $uniqueImgName.'.'.$imgExtension;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        } else
        {
            $news->image = $news->image;
        }
        $news->update([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'link' => $request->validated('link'),
            'visible' => $request->validated('visible'),
            'position' => $news->position
        ]);

        return $this->successResponse($this->jsonResponse($news));
    }

    public function changeVisibility($id)
    {
        $news = News::find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }
        $visibility = ($news->visible == 1) ? $news->visible = 0 : $news->visible = 1;
        $news->update(['visible' => $visibility]);
        return $this->successResponse($this->jsonResponse($news));
    }

    public function changePosition(DirectionRequest $request, $id)
    {
        $news = News::find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }
        $newsPosition = $news->position;
        if($request->direction == $request::UP_DIRECTION)
        {
            $nextNews = $this->getNext($news);
            if(!is_null($nextNews))
            {
                $news->update(['position' => $nextNews->position]);
                $nextNews->update(['position' => $newsPosition]);
            }
        }
        elseif($request->direction == $request::DOWN_DIRECTION)
        {
            $previousNews = $this->getPrevious($news);
            if(!is_null($previousNews))
            {
                $news->update(['position' => $previousNews->position]);
                $previousNews->update(['position' => $newsPosition]);
            }
        }
        return $this->successResponse($this->jsonResponse($news));
    }

    public function destroy($id)
    {
        $news = News::find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }
        $news->delete();
        return $this->successResponse($this->jsonResponse($news));
    }

    public function restore($id)
    {
        $news = News::withTrashed()->find($id);
        if(!$news)
        {
            return $this->errorResponse('No se encontro la noticia.', Response::HTTP_NOT_FOUND);
        }
        $news->restore();
        return $this->successResponse($this->jsonResponse($news));
    }

    private function getNext($news)
    {
        return News::where('position', '<', $news->position)->orderByDesc('position')->first();
    }

    private function getPrevious($news)
    {
        return News::where('position', '>', $news->position)->orderBy('position', 'asc')->first();
    }

    public function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'title' => $data->title,
            'content' => $data->content,
            'link' => $data->link,
            'slug' => $data->slug,
            'image' => $data->image,
            'visible' => $data->visible,
        ];
    }

    private function getMaxPosition()
    {
        return News::where('visible', '=', 1)->max('position');
    }
}
