<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Represents an image using the gd library
	 *
	 * @package			PHPRum
	 * @subpackage		GD
	 */
	class GDImage
	{
		/**
		 * specifies an image resource
         * @var resource
	     * @access protected
		 */
		protected $img;


		/**
		 * Constructor
		 *
		 * load image resource
		 *
		 * @return void
		 * @access private
		 */
		function __construct( $img = null )
        {
            if( !is_null( $img ))
            {
                $this->load( $img );
            }
		}


		/**
		 * Destructor
		 *
		 * destroy image
		 *
		 * @return void
		 * @access private
		 */
		function __destruct()
        {
            $this->destroy();
		}


		/**
		 * create an image resource
		 *
		 * @param   mixed	$src	image resource or image stream
		 *
		 * @return void
		 * @access protected
		 */
		protected function load( $src )
        {
			if( is_resource( $src ))
            {
				return $this->loadResource( $src );
			}
			else
            {
    			return $this->loadStream( $src );
			}
		}


		/**
		 * set image resource handle
		 *
		 * @param  resource   $img		image resource
		 *
		 * @return bool					true if successfull
		 * @access public
		 */
		function loadResource( &$src )
        {
			if( is_resource( $src ))
            {
				$this->img = $src;
			}
			else
            {
                throw new \System\Base\InvalidOperationException("not a valid resource");
            }
		}


		/**
		 * create an image resource from an image stream
		 *
		 * @return void
		 * @access private
		 */
		function loadStream( $src )
        {
			if( is_string( $src ))
            {
                $this->img = imagecreatefromstring( $src );

                if($this->img === false)
                {
                    throw new \System\Base\InvalidOperationException("image type is unsupported, the data is not in a recognised format, or the image is corrupt and cannot be loaded");
                }
			}
			else
            {
                throw new \System\Base\InvalidArgumentException("invalid type");
            }
		}


		/**
		 * create an image resource from a file or URL
		 *
		 * @param  string   $src		 		file or URL
		 * @param  string   $type		 		image type
		 *
		 * @return void
		 * @access public
		 */
		function loadFile( $src, $type = 'image/jpeg' )
		{
			if( file_exists( $src ))
            {
				// create from jpeg image
				if( $type === 'image/jpeg' ||
					$type === 'image/pjpeg' ) {
                    $this->img = imagecreatefromjpeg( $src );
				}

				// create from png image
				elseif( $type === 'image/png' ) {
					$this->img = imagecreatefrompng( $src );
				}

				// create from gif image
				elseif( $type === 'image/gif' ) {
					$this->img = imagecreatefromgif( $src );
				}

				// create from bmp image
				elseif( $type === 'image/wbmp' ) {
					$this->img = imagecreatefromwbmp( $src );
				}

                if($this->img === false)
                {
                    throw new \System\Base\InvalidOperationException("image type is unsupported, the data is not in a recognised format, or the image is corrupt and cannot be loaded");
                }
			}
			else
            {
                throw new \System\Utils\FileNotFoundException("file {$src} doesa not exist");
            }
		}


		/**
		 * return image width
		 *
		 * @return int							width in pixels
		 * @access public
		 */
		function getWidth()
        {
			if( is_resource( $this->img ))
            {
				return imagesx( $this->img );
			}
			else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * return image height
		 *
		 * @return int							height in pixels
		 * @access public
		 */
		function getHeight()
        {
			if( is_resource( $this->img ))
            {
				return imagesy( $this->img );
			}
			else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * return resource
		 *
		 * @return resource
		 * @access public
		 */
		function getResource()
        {
			if( is_resource( $this->img ))
            {
                return $this->img;
            }
			else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * return binary image stream
		 *
		 * @param  string   $type		 		image type
         * @param  int      $quality		 	image quality out of 0/100
		 *
		 * @return string
		 * @access public
		 */
		function getStream( $type = 'image/jpeg', $quality = 75 )
        {
			ob_start();

            if( is_resource( $this->img ))
            {
                $result = false;

				// create jpeg image
				if( $type === 'image/jpeg' ||
					$type === 'image/pjpeg' )
                {
					$result = imagejpeg( $this->img, null, $quality );
				}

				// create png image
				elseif( $type === 'image/png' )
                {
					$result = imagepng( $this->img );
				}

				// create gif image
				elseif( $type === 'image/gif' )
                {
					$result = imagegif( $this->img );
				}

				// create bmp image
				elseif( $type === 'image/wbmp' )
                {
					$result = imagewbmp( $this->img );
				}

                else
                {
                    throw new \System\Base\InvalidArgumentException("type {$type} not supported");
                }

                if( $result !== true )
                {
                    throw new \System\Base\InvalidOperationException("could not output image stream");
                }
			}
			else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }

            return ob_get_clean();
		}


		/**
		 * output image resource to file or browser
		 *
		 * @param  string   $path 				path to file
		 * @param  string   $type		 		image type
         * @param  int      $quality		 	image quality out of 0/100
		 *
		 * @return void
		 * @access public
		 */
		function output( $path, $type = 'image/jpeg', $quality = 75 )
		{
			if( is_resource( $this->img ))
            {
                $result = false;

				// create jpeg image
				if( $type === 'image/jpeg' ||
					$type === 'image/pjpeg' )
                {
					$result = imagejpeg( $this->img, $path, $quality );
				}

				// create png image
				elseif( $type === 'image/png' )
                {
					$result = imagepng( $this->img, $path );
				}

				// create gif image
				elseif( $type === 'image/gif' )
                {
					$result = imagegif( $this->img, $path );
				}

				// create bmp image
				elseif( $type === 'image/wbmp' )
                {
					$result = imagewbmp( $this->img, $path );
				}

                else
                {
                    throw new \System\Base\InvalidArgumentException("type {$type} not supported");
                }

                if( $result !== true )
                {
                    throw new \System\Base\InvalidOperationException("could not output image");
                }
			}
			else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * output image as stream, note
		 *
		 * @param  string   $type	 			image type
		 *
		 * @return void
		 * @access public
		 */
		function render( $type = 'image/jpeg' )
        {
			// nothing can be rendered before or after
			ob_start();

			// output image to buffer
			echo $this->getStream( $type );

			$len = ob_get_length();

            // output headers
            header("Content-type: $type");
            header("Content-Length: $len");

            // clear the buffer and stream image to browser
            ob_end_flush();
		}


		/**
		 * returns a copy of the GDImage object or null on failure
		 *
		 * @return GDImage					image object
		 * @access public
		 */
		function copy()
        {
			if( is_resource( $this->img ))
            {
				return new GDImage( $this->getStream() );
			}
            else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * turns on | off alphablending
		 *
		 * @param  bool     $on			 		on | off
		 *
		 * @return bool							true if successfull
		 * @access public
		 */
		function alphablending( $on = true )
        {
			if( is_resource( $this->img ))
            {
				return imagealphablending( $this->img, (bool) $on );
			}
            else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * turns on | off antialiasing
		 *
		 * @param  bool     $on			 		on | off
		 *
		 * @return bool							true if successfull
		 * @access public
		 */
		function antialias( $on = true )
        {
			if( function_exists('imageantialias'))
			{
				if( is_resource( $this->img ))
				{
					return imageantialias( $this->img, (bool) $on );
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GDImage has no image");
				}
			}
            else
            {
                throw new \System\Base\InvalidOperationException("the required GD function imageantialias is only available if PHP is compiled with the bundled version of the GD library");
            }
		}


		/**
		 * rotate the image based on parameters
		 *
		 * @param  int		$angle	     	image width
		 * @param  int   	$bg		    	background color (hex)
		 *
		 * @return void
		 * @access public
		 */
		function rotate( $angle = 90, $bg = -1 )
        {
			if( function_exists( 'imagerotate' ))
			{
				if( is_resource( $this->img ))
				{
					$img = imagerotate( $this->img, (real) $angle, $bg );

					imagedestroy($this->img);
					$this->img = $img;
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GDImage has no image");
				}
			}
            else
            {
                throw new \System\Base\InvalidOperationException("the required GD function imagerotate is only available if PHP is compiled with the bundled version of the GD library");
            }
		}


		/**
		 * resizes the image based on parameters
		 *
		 * @param  int					$width	     	image width
		 * @param  int					$height	    	image height
		 * @param  GDImageResizeMode   	$mode     	 	scale mode
		 *
		 * @return void
		 * @access public
		 */
		function resize( $width, $height, GDImageResizeMode $mode = null )
		{
			if((int)$width>0 && (int)$height>0)
			{
				$mode = $mode?$mode:GDImageResizeMode::scaleToFit();

				if( is_resource( $this->img ))
				{
					$width = (int) $width;
					$height = (int) $height;

					// get ratio x:y
					$ratio = ( imagesx( $this->img ) / imagesy( $this->img ));

					/**
					 * get image width and
					 * height based on scale mode
					 */

					// __SCALE_TO_FIT__
					if( $mode == GDImageResizeMode::scaleToFit() )
					{
						// new height is smaller than actual height
						if( $height < imagesy( $this->img ))
						{
							// set height to specified & resize width
							$newwidth = ( $height * imagesx( $this->img )) / imagesy( $this->img );

							// new width is smaller than actual width
							if( $newwidth > $width )
							{
								// set width to specified & resize height
								$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
							}
							else
							{
								$width = $newwidth;
							}
						}
						// new width is smaller than actual width
						elseif( $width < imagesx( $this->img ))
						{
							// set width to specified & resize height
							$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
						}
						else
						{
							// image actual image is smaller than specified image
							$height = imagesy( $this->img );
							$width = imagesx( $this->img );
						}
					}

					// __SCALE_TO_CROP__
					if( $mode == GDImageResizeMode::scaleToCrop() )
					{
						// new height is smaller than actual height
						if( $height < imagesy( $this->img ))
						{
							// set height to specified & resize width
							$newwidth = ( $height * imagesx( $this->img )) / imagesy( $this->img );

							// new width is smaller than actual width
							if( $newwidth < $width )
							{
								// set width to specified & resize height
								$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
							}
							else
							{
								$width = $newwidth;
							}
						}
						// new width is smaller than actual width
						elseif( $width < imagesx( $this->img ))
						{
							// set width to specified & resize height
							$newheight = ( $width * imagesy( $this->img )) / imagesx( $this->img );

							// new width is smaller than actual width
							if( $newheight < $height )
							{
								// set width to specified & resize height
								$width = ( $height * imagesx( $this->img )) / imagesy( $this->img );
							}
							else
							{
								$height = $newheight;
							}
						}
						else
						{
							// image actual image is smaller than specified image
							if($height < imagesy( $this->img ))
							{
								$width = ( $height * imagesx( $this->img )) / imagesy( $this->img );
							}
							elseif($width < imagesx( $this->img ))
							{
								$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
							}
							elseif($width > imagesx( $this->img ) && $height > imagesy( $this->img ))
							{
								// set height to specified & resize width
								$newwidth = ( $height * imagesx( $this->img )) / imagesy( $this->img );

								// new width is smaller than actual width
								if( $newwidth < $width )
								{
									// set width to specified & resize height
									$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
								}
								else
								{
									$width = $newwidth;
								}
							}
							else
							{
								$height = imagesy( $this->img );
								$width = imagesx( $this->img );
							}
						}
					}

					// SCALE_TO_WIDTH
					else if( $mode == GDImageResizeMode::scaleToWidth() )
					{
						// set width to specified & resize height
						$height = ( $width * imagesy( $this->img )) / imagesx( $this->img );
					}

					// SCALE_TO_HEIGHT
					else if( $mode == GDImageResizeMode::scaleToHeight() )
					{
						// set height to specified & resize width
						$width = ( $height * imagesx( $this->img )) / imagesy( $this->img );
					}

					/**
					 * create new image resource
					 * based on new dimensions
					 */

					// create image
					$img = imagecreatetruecolor( $width, $height );

					if( !$img )
					{
						throw new \System\Base\InvalidOperationException("could not create empty resource");
					}

					// adjust for transparent background
					$trnprt_indx = imagecolortransparent($this->img);

					/*
					if ($trnprt_indx >= 0 && 0)
					{
						// Get the original image's transparent color's RGB values
						$trnprt_color = imagecolorsforindex($this->img, $trnprt_indx);

						// Allocate the same color in the new image resource
						$trnprt_indx = imagecolorallocate($img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

						// Completely fill the background of the new image with allocated color.
						imagefill($img, 0, 0, $trnprt_indx);

						// Set the background color for new image to transparent
						imagecolortransparent($img, $trnprt_indx);
					}
					*/

					// Turn off transparency blending (temporarily)
					imagealphablending($img, false);

					// Create a new transparent color for image
					$color = imagecolorallocatealpha($img, 0, 0, 0, 127);

					// Completely fill the background of the new image with allocated color.
					imagefill($img, 0, 0, $color);

					// Restore transparency blending
					imagesavealpha($img, true);

					// copy image
					if( !imagecopyresampled( $img, $this->img, 0, 0, 0, 0, $width, $height, imagesx( $this->img ), imagesy( $this->img )))
					{
						throw new \System\Base\InvalidOperationException("could not copy or resample image");
					}

					// return image
					if( !imagedestroy( $this->img ))
					{
						throw new \System\Base\InvalidOperationException("could not create destroy resource");
					}

					// set new image
					$this->img = $img;
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GDImage has no image");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("width and height must be greater than 0");
			}
		}


		/**
		 * crops the image based on parameters
		 *
		 * @param  int		$x_coord     	image x coord
		 * @param  int   	$y_coord    	image y coord
		 * @param  int		$width	     	image width
		 * @param  int   	$height	    	image height
		 *
		 * @return void
		 * @access public
		 */
        function crop( $x_coord, $y_coord, $width, $height )
        {
			if((int)$width>0 && (int)$height>0)
			{
				if( is_resource( $this->img ))
				{
					$dst_image = imagecreatetruecolor($width, $height);
					$dst_x = 0;
					$dst_y = 0;
					$src_x = $x_coord;
					$src_y = $y_coord;
					$dst_w = imagesx( $this->img );
					$dst_h = imagesy( $this->img );
					$src_w = imagesx( $this->img );
					$src_h = imagesy( $this->img );

					imagecopyresampled($dst_image, $this->img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
					imagedestroy($this->img);

					$this->img = $dst_image;
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GDImage has no image");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("width and height must be greater than 0");
			}
        }


		/**
		 * merges the image with another
		 *
		 * @param  GDImage	$image	image to merge with
		 * @param  int		$x_coord     	image x coord
		 * @param  int   	$y_coord    	image y coord
		 * @param  int   	$quality    	quality
		 *
		 * @return void
		 * @access public
		 */
		function merge( GDImage $image, $x_coord, $y_coord, $quality = 100 )
		{
			if( is_resource( $this->img ))
            {
				$src_image = $image->getResource();
				$dst_x = $x_coord;
				$dst_y = $y_coord;
				$src_x = 0;
				$src_y = 0;
				$dst_w = imagesx( $this->img );
				$dst_h = imagesy( $this->img );
				$src_w = $image->getWidth();
				$src_h = $image->getHeight();

				imagecopy($this->img, $src_image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
            }
            else
            {
                throw new \System\Base\InvalidOperationException("GDImage has no image");
            }
		}


		/**
		 * destroy the image and release the memory
		 *
		 * @return void
		 * @access public
		 */
		function destroy()
        {
			if( is_resource( $this->img ))
            {
				imagedestroy( $this->img );
			}
		}
	}
?>