<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $nombre_parcela;
    public $nombre_cultivo;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $nombre_parcela, $nombre_cultivo)
    {
        $this->name = $name;
        $this->nombre_parcela = $nombre_parcela;
        $this->nombre_cultivo = $nombre_cultivo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from('sistema@tuempresa.com', 'Sistema TID4AGRO')
                      ->subject('🌱 Notificación Sistema Agrícola - Nuevo Ciclo de Vida')
                      ->view('notificacion')
                      ->with([
                          'name' => $this->name,
                          'nombre_parcela' => $this->nombre_parcela,
                          'nombre_cultivo' => $this->nombre_cultivo,
                      ]);

        // Opcional: Si quieres adjuntar los logos como archivos embebidos para mejor compatibilidad
        $logos = [
            'image.png',
            'logocicy.png',
            'logoeuropa.png',
            'logojunta.png',
            'logorefex.png',
            'TID4AGRO.png'
        ];

        foreach ($logos as $logo) {
            $logoPath = public_path('imagenes/logos/' . $logo);
            if (file_exists($logoPath)) {
                $email->attach($logoPath, [
                    'as' => $logo,
                    'mime' => $this->getMimeType($logo),
                ]);
            }
        }

        return $email;
    }

    private function getMimeType($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml'
        ];

        return $mimeTypes[strtolower($extension)] ?? 'image/png';
    }
}
