<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materiais = Material::orderBy('equipamento')
            ->orderBy('marca')
            ->paginate(10);

        return view('admin.materiais.index', compact('materiais'));
    }

    public function create()
    {
        return view('admin.materiais.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipamento' => ['required', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'unidade' => ['required', 'string', 'max:20'],
            'ativo' => ['nullable'],
        ]);

        $data['ativo'] = $request->has('ativo');

        Material::create($data);

        return redirect()->route('admin.materiais.index')
            ->with('success', 'Material cadastrado com sucesso!');
    }

    public function edit(Material $material)
    {
        return view('admin.materiais.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'equipamento' => ['required', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'unidade' => ['required', 'string', 'max:20'],
            'ativo' => ['nullable'],
        ]);

        $data['ativo'] = $request->has('ativo');

        $material->update($data);

        return redirect()->route('admin.materiais.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('admin.materiais.index')
            ->with('success', 'Material excluído com sucesso!');
    }
}
