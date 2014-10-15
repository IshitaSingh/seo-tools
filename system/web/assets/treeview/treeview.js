

	/**
	* treeviewToggleNode
	* 
	* toggle node status
	*
	* @param		controlId   name of control
	* @param		nodeId		id of node
	* @param		uri			uri for ajax callback
	* @param		params		paramaters
	* 
	* @return	bool						TRUE if successfull
	*/
	Rum.treeviewToggleNode = function( controlId, nodeId, uri, params ) {

		node = document.getElementById( controlId + '__node_' + nodeId );

		if(node) {
			branch = node.getElementsByTagName( 'a' ).item(0);

			children = node.getElementsByTagName( 'ul' ).item(0);

			if(children) {

				if( children.style.display == 'none' ) {
					children.style.display = 'block';

					img = node.getElementsByTagName( 'img' ).item(0);

					if(img) {
						img.className = 'folder_expanded';
					}

					if( branch.className == 'fcollapsed' ) {
						branch.className = 'fexpanded';
					}
					else {
						branch.className = 'expanded';
					}

					// toggle state
					if( uri ) {
						params += '&' + controlId + '__' + nodeId + '_expand=1&' + controlId + '_submitted=1';
					}
				}
				else {
					children.style.display = 'none';

					img = node.getElementsByTagName( 'img' ).item(0);

					if(img) {
						img.className = 'folder_collapsed';
					}

					if( branch.className == 'fexpanded' ) {
						branch.className = 'fcollapsed';
					}
					else {
						branch.className = 'collapsed';
					}

					// toggle state
					if( uri ) {
						params += '&' + controlId + '__' + nodeId + '_collapse=1&' + controlId + '_submitted=1';
					}
				}

				return Rum.sendAsync( uri, params, 'POST' );
			}
		}

		return false;
	}

