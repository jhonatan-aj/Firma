<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tramite;
use App\Models\Requisito;
use App\Models\Formato;
use App\Models\Membrete;
use App\Models\Nivel;
use App\Models\Mencion;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProcesoTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Niveles y Menciones
        $nivel = Nivel::firstOrCreate(
            ['codigo' => 'PREG'],
            ['nivel' => 'Pregrado', 'estado' => true]
        );
        $mencion = Mencion::firstOrCreate(
            ['codigo' => 'SIST'],
            [
                'mencion' => 'Ingeniería de Sistemas',
                'nivel' => $nivel->id,
                'facultad' => '09',
                'estado' => true
            ]
        );

        // 2. Crear Personas (Tesistas y Autoridades)
        $tesista1 = Persona::firstOrCreate(['dni' => '11111111'], [
            'codigo' => '20150001',
            'paterno' => 'García',
            'materno' => 'López',
            'nombres' => 'Juan',
            'correo' => 'juan@example.com',
            'celular' => '999888777'
        ]);

        $tesista2 = Persona::firstOrCreate(['dni' => '22222222'], [
            'codigo' => '20150002',
            'paterno' => 'Martínez',
            'materno' => 'Ruiz',
            'nombres' => 'María',
            'correo' => 'maria@example.com',
            'celular' => '999888776'
        ]);

        $autoridad = Persona::firstOrCreate(['dni' => '33333333'], [
            'codigo' => 'DOC001',
            'paterno' => 'Pérez',
            'materno' => 'Sánchez',
            'nombres' => 'Roberto',
            'correo' => 'roberto@example.com',
            'celular' => '999888775'
        ]);

        // 3. Crear Usuarios
        $user1 = Usuario::firstOrCreate(['persona_id' => $tesista1->id], [
            'usuario' => 'juan.garcia',
            'password' => Hash::make('password'),
            'estado' => true
        ]);

        $user2 = Usuario::firstOrCreate(['persona_id' => $tesista2->id], [
            'usuario' => 'maria.martinez',
            'password' => Hash::make('password'),
            'estado' => true
        ]);

        $userAuth = Usuario::firstOrCreate(['persona_id' => $autoridad->id], [
            'usuario' => 'roberto.perez',
            'password' => Hash::make('password'),
            'estado' => true
        ]);

        // 4. Crear Membrete y Formato
        $membrete = Membrete::firstOrCreate(['nombre' => 'Membrete General'], [
            'nivel_id' => $nivel->id,
            'nivel_filtro' => 'Pregrado',
            'centro' => '<h3>UNIVERSIDAD NACIONAL HERMILIO VALDIZAN</h3>',
            'derecha' => 'logo_derecha.png',
            'izquierda' => 'logo_izquierda.png',
            'estado' => true
        ]);

        $contenidoFormato = '
        <div class="documento">
            <h2 style="text-align: center;">SOLICITUD DE APROBACIÓN</h2>
            <br>
            <p><strong>Para:</strong> <<< Autoridad >>></p>
            <p><strong>De:</strong> <<< Tesistas >>></p>
            <p><strong>Asunto:</strong> <<< Asunto >>></p>
            <p><strong>Fecha:</strong> <<< Fecha >>></p>
            <hr>
            <h3>Datos del Trabajo:</h3>
            <ul>
                <li><strong>Título:</strong> <<< Titulo >>></li>
                <li><strong>Nivel:</strong> <<< Nivel >>></li>
                <li><strong>Mención:</strong> <<< Mencion >>></li>
                <li><strong>Asesor:</strong> <<< Asesor >>></li>
            </ul>
            <h3>Fundamentación:</h3>
            <p><<< Contenido >>></p>
            <br><br>
            <div class="firmas" style="text-align: center;">
                <p>_______________________</p>
                <p><<< Tesistas >>></p>
            </div>
        </div>';

        $formato = Formato::firstOrCreate(['nombre' => 'Solicitud de Tesis'], [
            'membrete_id' => $membrete->id,
            'tipo' => 'solicitud',
            'contenido' => $contenidoFormato,
            'estado' => true,
            'utilizado' => true
        ]);

        // 5. Crear Trámite y Requisitos
        $tramite = Tramite::firstOrCreate(['codigo' => 'TR-TESIS-01'], [
            'nombre' => 'Aprobación de Plan de Tesis',
            'descripcion' => 'Trámite para aprobar el plan de tesis inicial',
            'estado' => true,
            'obligatorio' => true,
            'dirigido' => true,
            'tipo' => 'tesis'
        ]);

        // Asociar formato al trámite (usando la nueva tabla pivot si existe, o la antigua)
        // Verificamos si existe la relación en el modelo
        if (!$tramite->formatos()->where('formato_id', $formato->id)->exists()) {
            $tramite->formatos()->attach($formato->id);
        }

        Requisito::firstOrCreate(['nombre' => 'Plan de Tesis (PDF)', 'tramite_id' => $tramite->id], [
            'descripcion' => 'Documento del plan de tesis en formato PDF',
            'estado' => true,
            'tipo' => 'obligatorio'
        ]);

        Requisito::firstOrCreate(['nombre' => 'Carta de Aceptación de Asesor', 'tramite_id' => $tramite->id], [
            'descripcion' => 'Carta firmada por el asesor',
            'estado' => true,
            'tipo' => 'obligatorio'
        ]);

        $this->command->info('Datos de prueba creados correctamente:');
        $this->command->info("- Trámite ID: {$tramite->id}");
        $this->command->info("- Formato ID: {$formato->id}");
        $this->command->info("- Usuarios IDs: {$user1->id}, {$user2->id}, {$userAuth->id}");
    }
}
