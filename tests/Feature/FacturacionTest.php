<?php

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\User;

// Regla de negocio: tipo de factura según la condición de IVA del cliente
it('asigna el tipo de factura según la condición de IVA', function (string $condicion, string $esperado) {
    $cliente = new Cliente(['condicion_iva' => $condicion]);

    expect(Comprobante::tipoPara($cliente))->toBe($esperado);
})->with([
    'responsable inscripto' => ['responsable_inscripto', 'A'],
    'monotributista' => ['monotributista', 'B'],
    'exento' => ['exento', 'B'],
    'consumidor final' => ['consumidor_final', 'B'],
]);

it('emite siempre factura C si el emisor es monotributista', function () {
    $cliente = new Cliente(['condicion_iva' => 'responsable_inscripto']);

    expect(Comprobante::tipoPara($cliente, 'monotributista'))->toBe('C');
});

// Seguridad: sin iniciar sesión no se puede entrar
it('redirige al login si no hay sesión iniciada', function () {
    $this->get(route('clientes.index'))->assertRedirect(route('login'));
});

// Alta de clientes y validaciones
it('crea un cliente con datos válidos', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('clientes.store'), [
            'razon_social' => 'Panadería Don Pepe',
            'cuit' => '20123456789',
            'condicion_iva' => 'monotributista',
        ])
        ->assertRedirect(route('clientes.index'));

    $this->assertDatabaseHas('clientes', ['cuit' => '20123456789']);
});

it('rechaza un CUIT que no tiene 11 números', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('clientes.store'), [
            'razon_social' => 'Cliente con CUIT mal',
            'cuit' => '20-1234',
            'condicion_iva' => 'monotributista',
        ])
        ->assertSessionHasErrors('cuit');
});

it('no permite dos clientes con el mismo CUIT', function () {
    Cliente::create(['razon_social' => 'Primero', 'cuit' => '20123456789', 'condicion_iva' => 'exento']);

    $this->actingAs(User::factory()->create())
        ->post(route('clientes.store'), [
            'razon_social' => 'Segundo',
            'cuit' => '20123456789',
            'condicion_iva' => 'exento',
        ])
        ->assertSessionHasErrors('cuit');
});

// Emisión de comprobantes
it('emite comprobantes con tipo y numeración automáticos', function () {
    $cliente = Cliente::create(['razon_social' => 'Empresa SA', 'cuit' => '30123456789', 'condicion_iva' => 'responsable_inscripto']);
    $usuario = User::factory()->create();

    foreach ([1000, 2500] as $importe) {
        $this->actingAs($usuario)->post(route('comprobantes.store'), [
            'cliente_id' => $cliente->id,
            'fecha' => now()->toDateString(),
            'importe' => $importe,
        ])->assertRedirect(route('comprobantes.index'));
    }

    $numeros = Comprobante::orderBy('numero')->get()->map->numeroCompleto()->all();

    expect($numeros)->toBe(['A 0001-00000001', 'A 0001-00000002']);
});

it('no permite eliminar un cliente que tiene comprobantes', function () {
    $cliente = Cliente::create(['razon_social' => 'Con facturas', 'cuit' => '27123456789', 'condicion_iva' => 'consumidor_final']);
    Comprobante::create([
        'cliente_id' => $cliente->id,
        'tipo' => 'B',
        'punto_venta' => 1,
        'numero' => 1,
        'fecha' => now()->toDateString(),
        'importe' => 500,
    ]);

    $this->actingAs(User::factory()->create())
        ->delete(route('clientes.destroy', $cliente))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
});

// Ficha del cliente: total facturado
it('muestra el total facturado a un cliente', function () {
    $cliente = Cliente::create(['razon_social' => 'Cliente Ficha', 'cuit' => '30111111119', 'condicion_iva' => 'responsable_inscripto']);

    Comprobante::create(['cliente_id' => $cliente->id, 'tipo' => 'A', 'punto_venta' => 1, 'numero' => 1, 'fecha' => '2026-01-10', 'importe' => 1000]);
    Comprobante::create(['cliente_id' => $cliente->id, 'tipo' => 'A', 'punto_venta' => 1, 'numero' => 2, 'fecha' => '2026-02-15', 'importe' => 2500]);

    $this->actingAs(User::factory()->create())
        ->get(route('clientes.show', $cliente))
        ->assertOk()
        ->assertSee('$ 3.500,00');
});

it('filtra el total facturado por rango de fechas', function () {
    $cliente = Cliente::create(['razon_social' => 'Cliente Filtro', 'cuit' => '30222222229', 'condicion_iva' => 'responsable_inscripto']);

    Comprobante::create(['cliente_id' => $cliente->id, 'tipo' => 'A', 'punto_venta' => 1, 'numero' => 1, 'fecha' => '2026-01-10', 'importe' => 1000]);
    Comprobante::create(['cliente_id' => $cliente->id, 'tipo' => 'A', 'punto_venta' => 1, 'numero' => 2, 'fecha' => '2026-02-15', 'importe' => 2500]);

    $this->actingAs(User::factory()->create())
        ->get(route('clientes.show', [$cliente, 'desde' => '2026-02-01', 'hasta' => '2026-02-28']))
        ->assertOk()
        ->assertSee('$ 2.500,00')
        ->assertDontSee('$ 1.000,00');
});
