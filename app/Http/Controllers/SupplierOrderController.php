<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\DeleteTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Repositories\TagRepository;
use App\Services\TagService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierOrderController
{
    public function __construct(
        private TagService $service,
        private TagRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $tags = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

            return response()->json(['tags' => $tags], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar tags:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar tags'], 500);
        }
    }

    public function store(CreateTagRequest $request)
    {
        try {
            DB::beginTransaction();
            $tag = $this->service->create($request);

            if ($tag) {
                DB::commit();
                $tags = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['tags' => $tags, 'message' => 'Tag cadastrada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar tag:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar tag'], 500);
        }
    }

    public function update(UpdateTagRequest $request)
    {
        try {
            DB::beginTransaction();
            $tag = $this->service->update($request);

            if ($tag) {
                DB::commit();

                $tags = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['tags' => $tags, 'message' => 'Tag atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar tag:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar tag'], 500);
        }
    }

    public function destroy(DeleteTagRequest $request)
    {
        try {
            DB::beginTransaction();

            $tag = $this->repository->delete($request->route('tagID'));

            if ($tag) {
                DB::commit();
                $tags = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['tags' => $tags, 'message' => 'Tag excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir tag:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir tag'], 500);
        }
    }
}
