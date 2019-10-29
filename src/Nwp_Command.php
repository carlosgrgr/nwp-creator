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
        WP_CLI::log( 'Se va a crear un block en ' . TEMPLATEPATH . '/src/Blocks/' . ucfirst( $slug ) );
        if ( !file_exists( TEMPLATEPATH . '/src/Blocks/' . ucfirst( $slug ) ) ) {
            if (mkdir( TEMPLATEPATH . '/src/Blocks/' . ucfirst( $slug ), 0755, true ) ) {
                WP_CLI::log( 'Carpeta creada' );
            } else {
                WP_CLI::error( 'Error al crear carpeta' );
            }
        }

        if ( $file = fopen( TEMPLATEPATH . '/src/Blocks/' . ucfirst( $this->slug ) . '/My' . ucfirst( $this->slug ) . '.php', "a" ) ) {
            WP_CLI::log( 'Clase de cpt creado' );
            if ( copy( '../templates/cpt-class-default.txt', TEMPLATEPATH . '/src/Blocks/' . ucfirst( $this->slug ) . '/My' . ucfirst( $this->slug ) . '.php' ) ) {
                // ir corrigiendo
                $str = file_get_contents( './' . ucfirst( $this->slug ) . '/My' . ucfirst( $this->slug ) . '.php' );
                
                $str = str_replace("{slug}", $this->slug, $str);
                $str = str_replace("{name}", $this->name, $str);
                $str = str_replace("{Uslug}", ucfirst( $this->slug ), $str);
                
                file_put_contents( './' . ucfirst( $this->slug ) . '/My' . ucfirst( $this->slug ) . '.php', $str );
                
                echo "Contenido de la clase creada";
            } else {
                echo "Eror al crear contenido de la clase";
            }
        } else {
            echo "Error al crear la clase del bloque";
        }
        fclose($file);
    }

    private function delete_block( $slug )
    {
        WP_CLI::log( 'Se va a eliminar un block' );
    }

    private function create_cpt( $name, $slug )
    {
        WP_CLI::log( 'Se va a crear un cpt' );
    }

}