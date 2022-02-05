<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Repository extends Model 
{
    use HasFactory;

    public $fillable = [
        'name',
        'slug',
        'url',
        'is_public',
        'use_v_in_version',
        'public_key',
        'private_key',
        'last_released_at',
        'last_released_version',
        'public_key',
    ];

    public $hidden = ['private_key'];

    public $casts = [
        'last_released_at' => 'date:F j, Y',
        'is_public' => 'boolean',
        'use_v_in_version' => 'boolean',
    ];

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $rsaKey = openssl_pkey_new(array( 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA));
            $privateKey = openssl_pkey_get_private($rsaKey); 
            openssl_pkey_export($privateKey, $pem); //Private Key
            file_put_contents($path = storage_path('app/'. Str::slug($model->url) .'.pem'), $pem);
            chmod($path, 0600);
            $model->public_key = $model->sshEncodePublicKey($rsaKey); //Public Key
        });

        static::deleting(function ($repository) {
            $repository->releases()->delete();

            if (file_exists($path = storage_path('app/'. Str::slug($repository->url) .'.pem'))) {
                unlink($path);
            }
            
            app(Filesystem::class)->deleteDirectory(storage_path('app/'. Str::slug($repository->url)));
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function releases()
    {
        return $this->hasMany(Release::class);
    }

    public function toArray() 
    {
        $model = parent::toArray();

        if (auth()->check() && auth()->user()->hasTeamPermission($this->team, 'releaser')) {
            $this->hidden = [];
        } else {
            unset($model['public_key']);
        }

        return $model;
    }

    private function sshEncodePublicKey($privKey) {
        $keyInfo = openssl_pkey_get_details($privKey);
        $buffer  = pack("N", 7) . "ssh-rsa" .
        $this->sshEncodeBuffer($keyInfo['rsa']['e']) . 
        $this->sshEncodeBuffer($keyInfo['rsa']['n']);
        return "ssh-rsa " . base64_encode($buffer);
    }
    
    private function sshEncodeBuffer($buffer) {
        $len = strlen($buffer);
        if (ord($buffer[0]) & 0x80) {
            $len++;
            $buffer = "\x00" . $buffer;
        }
        return pack("Na*", $len, $buffer);
    }
}
