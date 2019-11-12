<?php

class Nwp_Command extends WP_CLI_Command
{
    /**
     * Create block and cpt files structure for nwp theme
     * 
     * ## OPTIONS
     * 
     * <type>
     * : String for block or cpt.
     * 
     * <action>
     * : String for action. create | delete
     * 
     * [--name=<Nombre_del_bloque_o_cpt>]
     * : String. Block or CPT name.
     * 
     * [--slug=<slug>]
     * : String. Bock or CPT slug.
     * 
     * [--description=<description>]
     * : String. Only for blocks. String to add a description.
     * 
     */    

    public function __invoke( $args, $assoc_args ) {
        $type               = array_shift( $args );
        $action             = array_shift( $args );
        $this->name         = \WP_CLI\Utils\get_flag_value( $assoc_args, 'name' );
        $this->slug         = \WP_CLI\Utils\get_flag_value( $assoc_args, 'slug' );
        $this->description  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'description' );


        switch ($type) {
            case 'block':
                if ( $action == "create" ) {
                    $this->create_block( $this->name, $this->slug, $this->description );
                } else {
                    $this->delete_block( $this->slug );
                }
                break;
            case 'cpt':
                if ( $action == 'create' ) {
                    $this->create_cpt( $this->name, $this->slug );
                } else {
                    $this->delete_cpt( $this->slug );
                }
                break;
            default:
                WP_CLI::error( 'Type not valid. Try "wp nwp block" or "wp nwp cpt"' );
                break;
        }
    }

    private function create_block( $name, $slug, $description )
    {
        $needle = '-';
        $lastPos = 0;
        $positions = array();
        $block_name = ucfirst( $slug );

        while ( ( $lastPos = strpos( $block_name, $needle, $lastPos ) ) !== false ) {
            $positions[] = $lastPos;
            $block_name[$lastPos + 1] = strtoupper( $block_name[$lastPos + 1] );
            $lastPos = $lastPos+ strlen($needle);
        }
        $block_name = str_replace( '-', '', $block_name ) . 'Block';

        $block_folder_path = '/src/Blocks/' . $block_name;

        WP_CLI::log( 'Se va a crear un bloque en ' . TEMPLATEPATH . $block_folder_path );
        if ( !file_exists( TEMPLATEPATH . $block_folder_path ) ) {
            if (mkdir( TEMPLATEPATH . $block_folder_path, 0755, true ) ) {
                WP_CLI::log( 'Carpeta creada' );
            } else {
                WP_CLI::error( 'Error al crear carpeta' );
            }
        }

        if ( $file = fopen( TEMPLATEPATH . $block_folder_path . '/My' . $block_name . '.php', 'a' ) ) {
            $block_class_path = $block_folder_path . '/My' . $block_name . '.php';
            
            if ( copy( __DIR__ . '/../templates/block-template.txt', TEMPLATEPATH . $block_class_path ) ) {
                $str = file_get_contents( TEMPLATEPATH . $block_class_path );
                $str = str_replace( "{slug}", $slug, $str );
                $str = str_replace( "{name}", $name, $str );
                $str = str_replace( "{blockName}", $block_name, $str );
                $str = str_replace( "{description}", $description, $str );
                
                file_put_contents( TEMPLATEPATH . $block_class_path, $str );
                
                WP_CLI::log( 'Contenido de la clase creada' );
            } else {
                WP_CLI::error( 'Eror al crear contenido de la clase' );
            }

            WP_CLI::log( 'Creando demás archivos' );
            
            $types = array( 'css', 'js', 'json', 'views' );
            foreach ( $types as $type ) {
                if ( !file_exists( TEMPLATEPATH . $block_folder_path . '/' . $type ) ) {
                    mkdir( TEMPLATEPATH . $block_folder_path . '/' . $type, 0755, true );
                }
                if ( $type != 'views' ) {
                    if ( $file = fopen(TEMPLATEPATH . $block_folder_path . '/' . $type . '/' . $slug . '.' . $type, "a" ) ) {
                        WP_CLI::log( 'Archivo ' . $type . ' creado' );
                    } else {
                        WP_CLI::log( 'Error al crear el archivo ' . $type );
                    }
                    fclose($file);
                } else {
                    if ( $file = fopen(TEMPLATEPATH . $block_folder_path . '/' . $type . '/content-block.php', "a" ) ) {
                        WP_CLI::log( 'Archivo ' . $type . ' creado' );
                    } else {
                        WP_CLI::log( 'Error al crear el archivo ' . $type );
                    }
                    fclose($file);
                }
            }

            WP_CLI::success( 'Bloque creado con éxito. Debes Inicializarlo en MyBlocks.' );

        } else {
            WP_CLI::error( 'Error al crear la clase del bloque.' );
        }
        fclose($file);
    }

    private function delete_block( $slug )
    {
        WP_CLI::log( 'Se va a eliminar un block' );

        $needle = '-';
        $lastPos = 0;
        $positions = array();
        $block_name = ucfirst( $slug );

        while ( ( $lastPos = strpos( $block_name, $needle, $lastPos ) ) !== false ) {
            $positions[] = $lastPos;
            $block_name[$lastPos + 1] = strtoupper( $block_name[$lastPos + 1] );
            $lastPos = $lastPos+ strlen($needle);
        }
        $block_name = str_replace( '-', '', $block_name ) . 'Block';

        $block_folder_path = '/src/Blocks/' . $block_name;

        if ( file_exists( TEMPLATEPATH . $block_folder_path ) ) {
           if( $this->rrmdir( TEMPLATEPATH . $block_folder_path ) ) {
               WP_CLI::success( 'Bloque eliminado con éxito.' );
           } else {
               WP_CLI::error( 'Ha habido algún problema borrando el bloque. Prueba a eliminar la carpeta ' . TEMPLATEPATH . $block_folder_path . ' manualmente.' );
           }
        } else {
            WP_CLI::error( 'No existe ningún bloque con ese slug.' );
        }
    }

    private function create_cpt( $name, $slug )
    {
        $needle = '-';
        $lastPos = 0;
        $positions = array();
        $cpt_name = ucfirst( $slug );

        while ( ( $lastPos = strpos( $cpt_name, $needle, $lastPos ) ) !== false ) {
            $positions[] = $lastPos;
            $cpt_name[$lastPos + 1] = strtoupper( $cpt_name[$lastPos + 1] );
            $lastPos = $lastPos+ strlen($needle);
        }
        $cpt_name = str_replace( '-', '', $cpt_name );

        $cpt_folder_path = '/src/' . $cpt_name;

        WP_CLI::log( 'Se va a crear un cpt en ' . TEMPLATEPATH . $cpt_folder_path );
        if ( !file_exists( TEMPLATEPATH . $cpt_folder_path ) ) {
            if (mkdir( TEMPLATEPATH . $cpt_folder_path, 0755, true ) ) {
                WP_CLI::log( 'Carpeta creada' );
            } else {
                WP_CLI::error( 'Error al crear carpeta' );
            }
        }

        if ( $file = fopen( TEMPLATEPATH . $cpt_folder_path . '/My' . $cpt_name . '.php', 'a' ) ) {
            $cpt_class_path = $cpt_folder_path . '/My' . $cpt_name . '.php';
            
            if ( copy( __DIR__ . '/../templates/cpt-template.txt', TEMPLATEPATH . $cpt_class_path ) ) {
                $str = file_get_contents( TEMPLATEPATH . $cpt_class_path );
                $str = str_replace( "{slug}", $slug, $str );
                $str = str_replace( "{name}", $name, $str );
                $str = str_replace( "{cptName}", $cpt_name, $str );
                
                file_put_contents( TEMPLATEPATH . $cpt_class_path, $str );
                
                WP_CLI::log( 'Contenido de la clase creada' );
            } else {
                WP_CLI::error( 'Eror al crear contenido de la clase' );
            }

            WP_CLI::log( 'Creando demás archivos' );
            
            $types = array( 'json', 'views' );
            foreach ( $types as $type ) {
                if ( !file_exists( TEMPLATEPATH . $cpt_folder_path . '/' . $type ) ) {
                    mkdir( TEMPLATEPATH . $cpt_folder_path . '/' . $type, 0755, true );
                }
                if ( $type != 'views' ) {
                    if ( $file = fopen(TEMPLATEPATH . $cpt_folder_path . '/' . $type . '/My' . $cpt_name . '.' . $type, "a" ) ) {
                        WP_CLI::log( 'Archivo ' . $type . ' creado' );
                    } else {
                        WP_CLI::log( 'Error al crear el archivo ' . $type );
                    }
                    fclose($file);
                } else {
                    if ( $file = fopen(TEMPLATEPATH . $cpt_folder_path . '/' . $type . '/content-' . $slug . '.php', "a" ) ) {
                        WP_CLI::log( 'Archivo ' . $type . ' creado' );
                    } else {
                        WP_CLI::log( 'Error al crear el archivo ' . $type );
                    }
                    fclose($file);
                }
            }

            WP_CLI::success( 'CPT creado con éxito. Debes Inicializarlo en MyApp.' );

        } else {
            WP_CLI::error( 'Error al crear la clase del bloque.' );
        }
        fclose($file);


    }

    private function delete_cpt( $slug )
    {
        WP_CLI::log( 'Se va a eliminar un cpt' );

        $needle = '-';
        $lastPos = 0;
        $positions = array();
        $cpt_name = ucfirst( $slug );

        while ( ( $lastPos = strpos( $cpt_name, $needle, $lastPos ) ) !== false ) {
            $positions[] = $lastPos;
            $cpt_name[$lastPos + 1] = strtoupper( $cpt_name[$lastPos + 1] );
            $lastPos = $lastPos+ strlen($needle);
        }
        $cpt_name = str_replace( '-', '', $cpt_name );

        $cpt_folder_path = '/src/' . $cpt_name;

        if ( file_exists( TEMPLATEPATH . $cpt_folder_path ) ) {
           if( $this->rrmdir( TEMPLATEPATH . $cpt_folder_path ) ) {
               WP_CLI::success( 'CPT eliminado con éxito.' );
           } else {
               WP_CLI::error( 'Ha habido algún problema borrando el CPT. Prueba a eliminar la carpeta ' . TEMPLATEPATH . $cpt_folder_path . ' manualmente.' );
           }
        } else {
            WP_CLI::error( 'No existe ningún CPT con ese slug.' );
        }
    }

    private function rrmdir($dir) 
    {
        
        if ( is_dir( $dir ) ) {
            $objects = scandir( $dir );
            foreach ( $objects as $object ) {
                if ( $object != "." && $object != ".." ) {
                    if ( is_dir( $dir."/".$object ) && !is_link( $dir."/".$object ) ){
                        $this->rrmdir( $dir."/".$object );
                    } else {
                        unlink( $dir."/".$object );
                    }
                }
            }
            if ( rmdir( $dir ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

}