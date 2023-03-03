<?php
// Security check
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
    // Autoload file exists, load the library
    require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

class Netwise_PDF_Generator {

    public function __construct() {
        add_action( 'init', array( $this, 'register_pdf_endpoint' ) );
        add_action( 'template_redirect', array( $this, 'generate_pdf_template' ) );
    }

    public function generate_post_pdf( $post_id ) {
        // Load the post content and title
        $post = get_post( $post_id );
        $post_title = $post->post_title;

        // Create a filename based on sanitized post title
        $filename = sanitize_title( $post_title ) . '.pdf';

        // Create a new MPDF instance
        $mpdf = new \Mpdf\Mpdf();

        // Convert post content to HTML
        $post_content = apply_filters( 'the_content', $post->post_content );

        // Replace image URLs with data URIs
        $post_content = preg_replace_callback( '/<img[^>]+>/i', function ( $matches ) {
            $img = $matches[0];
            $doc = new DOMDocument();
            @$doc->loadHTML( $img );
            $tags = $doc->getElementsByTagName( 'img' );
            foreach ( $tags as $tag ) {
                $url = $tag->getAttribute( 'src' );
                if ( strpos( $url, 'data:image' ) === false ) {
                    $image = file_get_contents( $url );
                    $type = pathinfo( $url, PATHINFO_EXTENSION );
                    $base64 = base64_encode( $image );
                    $img = str_replace( $url, 'data:image/' . $type . ';base64,' . $base64, $img );
                }
            }
            return $img;
        }, $post_content );

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';

        if ( has_custom_logo() ) {
            $logo_id = get_theme_mod( 'custom_logo' );
            $logo_image = wp_get_attachment_image_src( $logo_id, 'full' );
            $logo_url = '<img src="' . $logo_image[0] . '" alt="' . get_bloginfo( 'name' ) . ' Logo" style="max-height: 50px;">';
        } else {
            $logo_url = get_bloginfo( 'name' );
        }

        // Set a custom header with the site logo and page number
        $header = '<table width="100%">
                    <tr>
                        <td width="50%">' . $logo_url . '</td>
                        <td width="50%" align="right"><span>{PAGENO}</span></td>
                    </tr>
                </table><hr />';

        $footer = '<hr /><table width="100%"><tr><td width="50%">' . get_bloginfo( 'name' ) . '</td><td width="50%" style="text-align:right;">' . get_bloginfo( 'url' ) . '</td></tr></table>';

        $mpdf->SetHTMLHeader( $header );

        $mpdf->SetHTMLFooter( $footer );

        // Set the creator of the PDF to the site name
        $mpdf->SetCreator( get_bloginfo( 'name' ) );

        // Parse post content HTML into MPDF
        $mpdf->WriteHTML( '<h1>' . $post_title . '</h1>' );
        $mpdf->WriteHTML( '<strong>Uploaded on ' . get_the_date( '', $post_id ) .'</strong><br />' );

        // Write post content
        $mpdf->WriteHTML( $post_content );

        // Set the title of the PDF to the post title
        $mpdf->SetTitle( $post_title );

        // Output the PDF to the browser in a new tab
        $mpdf->Output( $filename, 'D' );
    }

    public function register_pdf_endpoint() {
        add_rewrite_endpoint( 'pdf', EP_PERMALINK );
    }

    public function generate_pdf_template() {
        global $wp_query;

        if ( isset( $wp_query->query_vars['pdf'] ) ) {
            $post_id = get_the_ID();
            $this->generate_post_pdf( $post_id );
            exit;
        }
    }
}

$netwise_pdf_generator = new Netwise_PDF_Generator();
