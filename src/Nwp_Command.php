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