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
                $this->create_cpt( $this->name, $this->slug );
                break;
            default:
                WP_CLI::error( 'Type not valid. Try "wp nwp block" or "wp nwp cpt"' );
                break;
        }
    }

    private function create_block( $name, $slug, $description )
    {
        $block_folder_path = '/src/Blocks/' . ucfirst( $slug ) . 'Block';


        WP_CLI::log( 'Se va a crear un block en ' . TEMPLATEPATH . $block_folder_path );
        if ( !file_exists( TEMPLATEPATH . $block_folder_path ) ) {
            if (mkdir( TEMPLATEPATH . $block_folder_path, 0755, true ) ) {
                WP_CLI::log( 'Carpeta creada' );
            } else {
                WP_CLI::error( 'Error al crear carpeta' );
            }
        }

        if ( $file = fopen( TEMPLATEPATH . $block_folder_path . '/My' . ucfirst( $slug ) . 'Block.php', 'a' ) ) {
            $block_class_path = $block_folder_path . '/My' . ucfirst( $slug ) . 'Block.php';
            
            if ( copy( __DIR__ . '/../templates/block-template.txt', TEMPLATEPATH . $block_class_path ) ) {
                $str = file_get_contents( TEMPLATEPATH . $block_class_path );
                $str = str_replace( "{slug}", $slug, $str );
                $str = str_replace( "{name}", $name, $str );
                $str = str_replace( "{title}", ucfirst( $name ), $str );
                $str = str_replace( "{Uslug}", ucfirst( $slug ), $str );
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
                        echo "Archivo $type creado";
                    } else {
                        echo "Error al crear el archivo $type";
                    }
                    fclose($file);
                } else {
                    if ( $file = fopen(TEMPLATEPATH . $block_folder_path . '/' . $type . '/content-block.php', "a" ) ) {
                        echo "Archivo $type creado";
                    } else {
                        echo "Error al crear el archivo $type";
                    }
                    fclose($file);
                }
            }

            WP_CLI::success( 'Bloque creado con éxito.' );

        } else {
            WP_CLI::error( 'Error al crear la clase del bloque.' );
        }
        fclose($file);
    }

    private function delete_block( $slug )
    {
        WP_CLI::log( 'Se va a eliminar un block' );
        $block_folder_path = '/src/Blocks/' . ucfirst( $slug ) . 'Block';
        WP_CLI::log( TEMPLATEPATH . $block_folder_path );
        if ( file_exists( TEMPLATEPATH . $block_folder_path ) ) {
           array_map( 'unlink', glob( TEMPLATEPATH . $block_folder_path . '/*/*/*.*' ) );
           array_map( 'unlink', glob( TEMPLATEPATH . $block_folder_path . '/*/*.*' ) );
           array_map( 'unlink', glob( TEMPLATEPATH . $block_folder_path . '/*.*' ) );
           array_map( 'rmdir', glob( TEMPLATEPATH . $block_folder_path . '/*/*' ) );
           array_map( 'rmdir', glob( TEMPLATEPATH . $block_folder_path . '/*' ) );
           if( rmdir( TEMPLATEPATH . $block_folder_path ) ) {
               WP_CLI::success( 'Bloque eliminado con éxito.' );
           } else {
               WP_CLI::error( 'Ha habido algún problema borrando el bloque. Prueba a eliminar la carpeta ' . TEMPLATEPATH . $block_folder_path . ' manualmente.' );
           }
        }
    }

    private function create_cpt( $name, $slug )
    {
        WP_CLI::log( 'Se va a crear un cpt' );
    }

    private function delete_cpt( $name, $slug )
    {
        WP_CLI::log( 'Se va a eliminar un cpt' );
    }

}