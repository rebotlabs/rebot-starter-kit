import { execSync } from 'node:child_process';
import type { Plugin } from 'vite';
import chokidar from 'chokidar';

interface TranslationExportOptions {
    /**
     * Whether to run the export on build
     */
    runOnBuild?: boolean;
    
    /**
     * Whether to watch for changes in language files during development
     */
    watchFiles?: boolean;
    
    /**
     * Paths to watch for changes (relative to project root)
     */
    watchPaths?: string[];
    
    /**
     * Command to run to export translations
     */
    exportCommand?: string;
}

const DEFAULT_OPTIONS: Required<TranslationExportOptions> = {
    runOnBuild: true,
    watchFiles: true,
    watchPaths: ['lang/**/*.php'],
    exportCommand: 'php artisan translations:export',
};

export function translationExport(userOptions: TranslationExportOptions = {}): Plugin {
    const options = { ...DEFAULT_OPTIONS, ...userOptions };
    
    const runExport = () => {
        try {
            console.log('🌐 Exporting translations...');
            execSync(options.exportCommand, { stdio: 'inherit' });
            console.log('✅ Translations exported successfully');
        } catch (error) {
            console.error('❌ Failed to export translations:', error);
        }
    };

    return {
        name: 'translation-export',
        buildStart() {
            // Always run on build start to ensure translations are up to date
            runExport();
        },
        configureServer(server) {
            if (!options.watchFiles) return;
            
            // Import chokidar dynamically since it's not in our dependencies
            const watcher = chokidar.watch(options.watchPaths, {
                ignored: /(^|[/\\])\../, // ignore dotfiles
                persistent: true,
                cwd: server.config.root
            });

            watcher.on('change', (path: string) => {
                console.log(`📝 Translation file changed: ${path}`);
                runExport();
                // Trigger HMR to reload the page/components using translations
                server.ws.send({
                    type: 'full-reload'
                });
            });

            watcher.on('add', (path: string) => {
                console.log(`📄 New translation file added: ${path}`);
                runExport();
                server.ws.send({
                    type: 'full-reload'
                });
            });

            server.httpServer?.on('close', () => {
                watcher.close();
            });
        }
    };
}
