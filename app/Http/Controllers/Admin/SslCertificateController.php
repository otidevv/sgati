<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SslCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SslCertificateController extends Controller
{
    // mimetypes cubre .crt/.key/.pfx (detectados como text/plain u octet-stream según el OS)
    private const FILE_TYPES = 'file|mimetypes:text/plain,application/x-x509-ca-cert,application/x-x509-user-cert,application/pkcs8,application/octet-stream,application/x-pem-file,application/x-pkcs12|max:10240';

    public function parseCert(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'cert_file'    => 'required|file|max:10240',
            'pfx_password' => 'nullable|string|max:255',
        ]);

        $file    = $request->file('cert_file');
        $content = file_get_contents($file->getRealPath());
        $ext     = strtolower($file->getClientOriginalExtension());

        // --- PFX / PKCS#12 ---
        if ($ext === 'pfx' || $ext === 'p12') {
            $password = $request->input('pfx_password', '');
            $certs    = [];

            if (!openssl_pkcs12_read($content, $certs, $password)) {
                // Intentar con contraseña vacía si falló
                if (!openssl_pkcs12_read($content, $certs, '')) {
                    return response()->json([
                        'error' => 'No se pudo leer el archivo PFX. Verifica que la contraseña sea correcta.',
                    ], 422);
                }
            }

            $parsed = openssl_x509_parse($certs['cert'] ?? '');
        } else {
            // --- CRT / PEM ---
            $parsed = openssl_x509_parse($content);
        }

        if (!$parsed) {
            return response()->json([
                'error' => 'No se pudo analizar el certificado. Asegúrate de subir el archivo .crt, .pem o .pfx principal.',
            ], 422);
        }

        $cn   = $parsed['subject']['CN'] ?? null;
        $org  = $parsed['issuer']['O']   ?? null;
        $issuerCn = $parsed['issuer']['CN'] ?? null;
        $issuer = $org ?? $issuerCn;
        $year = $parsed['validTo_time_t'] ? date('Y', $parsed['validTo_time_t']) : null;

        return response()->json([
            'file_type'       => ($ext === 'pfx' || $ext === 'p12') ? 'pfx' : 'crt',
            'common_name'     => $cn,
            'issuer'          => $issuer,
            'valid_from'      => $parsed['validFrom_time_t'] ? date('Y-m-d', $parsed['validFrom_time_t']) : null,
            'valid_until'     => $parsed['validTo_time_t']   ? date('Y-m-d', $parsed['validTo_time_t'])   : null,
            'name_suggestion' => trim(($cn ?? '') . ($year ? " — $year" : '')),
        ]);
    }

    public function index()
    {
        $certificates = SslCertificate::withCount('infrastructures')
            ->orderBy('valid_until')
            ->get();

        return view('admin.ssl-certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('admin.ssl-certificates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'issuer'      => 'nullable|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'notes'       => 'nullable|string',
            'cert_file'   => 'nullable|' . self::FILE_TYPES,
            'key_file'    => 'nullable|' . self::FILE_TYPES,
            'chain_file'  => 'nullable|' . self::FILE_TYPES,
            'pfx_file'    => 'nullable|' . self::FILE_TYPES,
        ]);

        $cert = new SslCertificate($data);
        $cert->save();

        if ($request->hasFile('pfx_file')) {
            $cert->pfx_file_path = $request->file('pfx_file')->store("ssl-certs/{$cert->id}/pfx", 'local');
        }
        if ($request->hasFile('cert_file')) {
            $cert->cert_file_path = $request->file('cert_file')->store("ssl-certs/{$cert->id}/cert", 'local');
        }
        if ($request->hasFile('key_file')) {
            $cert->key_file_path = $request->file('key_file')->store("ssl-certs/{$cert->id}/key", 'local');
        }
        if ($request->hasFile('chain_file')) {
            $cert->chain_file_path = $request->file('chain_file')->store("ssl-certs/{$cert->id}/chain", 'local');
        }
        $cert->save();

        return redirect()->route('admin.ssl-certificates.show', $cert)
            ->with('success', 'Certificado SSL registrado correctamente.');
    }

    public function show(SslCertificate $sslCertificate)
    {
        $sslCertificate->load('infrastructures.system');

        return view('admin.ssl-certificates.show', compact('sslCertificate'));
    }

    public function edit(SslCertificate $sslCertificate)
    {
        return view('admin.ssl-certificates.edit', compact('sslCertificate'));
    }

    public function update(Request $request, SslCertificate $sslCertificate)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'issuer'      => 'nullable|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'notes'       => 'nullable|string',
            'cert_file'   => 'nullable|' . self::FILE_TYPES,
            'key_file'    => 'nullable|' . self::FILE_TYPES,
            'chain_file'  => 'nullable|' . self::FILE_TYPES,
            'pfx_file'    => 'nullable|' . self::FILE_TYPES,
        ]);

        $sslCertificate->fill($data);

        if ($request->hasFile('pfx_file')) {
            $this->deleteFile($sslCertificate->pfx_file_path);
            $sslCertificate->pfx_file_path = $request->file('pfx_file')
                ->store("ssl-certs/{$sslCertificate->id}/pfx", 'local');
        }
        if ($request->hasFile('cert_file')) {
            $this->deleteFile($sslCertificate->cert_file_path);
            $sslCertificate->cert_file_path = $request->file('cert_file')
                ->store("ssl-certs/{$sslCertificate->id}/cert", 'local');
        }
        if ($request->hasFile('key_file')) {
            $this->deleteFile($sslCertificate->key_file_path);
            $sslCertificate->key_file_path = $request->file('key_file')
                ->store("ssl-certs/{$sslCertificate->id}/key", 'local');
        }
        if ($request->hasFile('chain_file')) {
            $this->deleteFile($sslCertificate->chain_file_path);
            $sslCertificate->chain_file_path = $request->file('chain_file')
                ->store("ssl-certs/{$sslCertificate->id}/chain", 'local');
        }

        foreach (['cert', 'key', 'chain', 'pfx'] as $type) {
            if ($request->boolean("remove_{$type}_file")) {
                $this->deleteFile($sslCertificate->{"{$type}_file_path"});
                $sslCertificate->{"{$type}_file_path"} = null;
            }
        }

        $sslCertificate->save();

        return redirect()->route('admin.ssl-certificates.show', $sslCertificate)
            ->with('success', 'Certificado SSL actualizado correctamente.');
    }

    public function destroy(SslCertificate $sslCertificate)
    {
        foreach (['cert_file_path', 'key_file_path', 'chain_file_path', 'pfx_file_path'] as $field) {
            $this->deleteFile($sslCertificate->$field);
        }

        $sslCertificate->delete();

        return redirect()->route('admin.ssl-certificates.index')
            ->with('success', 'Certificado SSL eliminado.');
    }

    public function downloadCert(SslCertificate $sslCertificate)
    {
        return $this->downloadFile($sslCertificate->cert_file_path, 'certificado');
    }

    public function downloadKey(SslCertificate $sslCertificate)
    {
        return $this->downloadFile($sslCertificate->key_file_path, 'llave-privada');
    }

    public function downloadChain(SslCertificate $sslCertificate)
    {
        return $this->downloadFile($sslCertificate->chain_file_path, 'cadena');
    }

    public function downloadPfx(SslCertificate $sslCertificate)
    {
        return $this->downloadFile($sslCertificate->pfx_file_path, 'certificado-pfx');
    }

    private function downloadFile(?string $path, string $label): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $disk = $this->disk();

        if (!$path || !$disk->exists($path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        $ext      = pathinfo($path, PATHINFO_EXTENSION);
        $filename = str($label)->slug()->value() . '.' . $ext;

        return $disk->download($path, $filename);
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            $this->disk()->delete($path);
        }
    }

    /** @return \Illuminate\Filesystem\FilesystemAdapter */
    private function disk(): \Illuminate\Filesystem\FilesystemAdapter
    {
        return Storage::disk('local');
    }
}
