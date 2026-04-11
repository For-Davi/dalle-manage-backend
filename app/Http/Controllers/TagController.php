<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\DeleteTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Repositories\TagRepository;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    public function __construct(
        private TagService $service,
        private TagRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $tags = $this->repository->getAllByEnterprise();

            return response()->json(['tags' => $tags], 200);
        }, 'Erro ao buscar tags', $request);
    }

    public function store(CreateTagRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('tag.create');
            $this->service->create($request);
            $tags = $this->repository->getAllByEnterprise();

            return response()->json(['tags' => $tags, 'message' => 'Tag cadastrada'], 201);
        }, 'Erro ao cadastrar tag', $request);
    }

    public function update(UpdateTagRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('tag.update');
            $this->service->update($request);
            $tags = $this->repository->getAllByEnterprise();

            return response()->json(['tags' => $tags, 'message' => 'Tag atualizada'], 200);
        }, 'Erro ao atualizar tag', $request);
    }

    public function destroy(DeleteTagRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('tag.delete');
            $this->repository->delete($request->route('tagID'));
            $tags = $this->repository->getAllByEnterprise();

            return response()->json(['tags' => $tags, 'message' => 'Tag excluída'], 200);
        }, 'Erro ao excluir tag', $request);
    }
}
