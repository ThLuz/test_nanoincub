<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Listagem de Funcionários</h2>

    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text" 
               placeholder="🔍 Digite o nome para filtrar..." 
               class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3 border">Nome</th>
                <th class="p-3 border">Login</th>
            </tr>
        </thead>
        <tbody>
            @forelse($funcionarios as $f)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border">{{ $f->nome }}</td>
                    <td class="p-3 border">{{ $f->login }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-3 text-center text-gray-500">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $funcionarios->links() }}
    </div>
</div>