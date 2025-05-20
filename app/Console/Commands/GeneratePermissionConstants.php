<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GeneratePermissionConstants extends Command
{
    protected $signature = 'permissions:generate-constants';
    protected $description = 'Generate TypeScript constants for permissions';

    public function handle()
    {
        $permissions = Permission::all()->pluck('name');
        
        $content = "// This file is auto-generated. DO NOT EDIT MANUALLY\n\n";
        $content .= "export const PERMISSIONS = {\n";
        
        foreach ($permissions as $permission) {
            $constantName = strtoupper(str_replace(' ', '_', $permission));
            $content .= "    {$constantName}: '{$permission}',\n";
        }
        
        $content .= "} as const;\n\n";
        $content .= "export type Permission = typeof PERMISSIONS[keyof typeof PERMISSIONS];\n";

        // Guardar en una ubicación accesible para el frontend
        file_put_contents(
            base_path('../frontend/src/constants/permissions.ts'),
            $content
        );

        $this->info('Permission constants generated successfully!');
    }
} 