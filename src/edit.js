import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaPlaceholder } from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { TextControl, SelectControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import './editor.scss';

export default function Edit(
  {
    context: { postType, postId, queryId },
  }
) {

  const { meta, title } = useSelect(
    ( select ) => {
      const { getEditedEntityRecord } =
      select( coreStore );
      const _meta = getEditedEntityRecord(
        'postType',
        postType,
        postId
        )?.meta;
      const _title = getEditedEntityRecord(
        'postType',
        postType,
        postId
        )?.title;
      return {
        meta: _meta,
        title: _title
      };
    },
    [ postType, postId ]
  );

  function sanitizeContractType( type ) {
    if ( type == 'Apprentissage' || type == 'Stage' ) {
      return 'Internship';
    }
    else {
      return type;
    }
  }
      
  const { editEntityRecord } = useDispatch( coreStore );
  const [ location, setLocation ] = useState( meta.location );
  const [ duration, setDuration ] = useState( meta.duree );
  const [ startdate, setStartDate ] = useState( meta.date_de_debut );
  const [ type, setType ] = useState( sanitizeContractType(meta.type_de_contrat) );
  const [ pdf, setPdf ] = useState( meta.lien_different );

  const updateMetaValue = ( newValue, field ) => {
    if (field == 'location') {
      const newMeta = { ...meta, location: newValue };
      editEntityRecord( 'postType', postType, postId, {
        meta: newMeta,
      } );
      setLocation( newValue );
    }
    if (field == 'duration' ) { 
      const newMeta = { ...meta, duree: newValue };
      editEntityRecord( 'postType', postType, postId, {
        meta: newMeta,
      } );
      setDuration( newValue );
    }
    if (field == 'date_de_debut' ) { 
      const newMeta = { ...meta, date_de_debut: newValue };
      editEntityRecord( 'postType', postType, postId, {
        meta: newMeta,
      } );
      setStartDate( newValue );
    }
    if (field == 'lien_different' ) { 
      const newMeta = { ...meta, lien_different: newValue };
      editEntityRecord( 'postType', postType, postId, {
        meta: newMeta,
      } );
      setPdf( newValue );
    }
    if (field == 'type_de_contrat' ) { 
      const newMeta = { ...meta, type_de_contrat: newValue };
      editEntityRecord( 'postType', postType, postId, {
        meta: newMeta,
      } );
      setType( newValue );
    }
  };
  const isDescendentOfQueryLoop = Number.isFinite( queryId );

  if ( isDescendentOfQueryLoop ) {
    return (
      <div className='career-data'>  
        <h3 className='wp-block-post-title career-title'>{title}</h3>
        <div className='location'><span className='label'>Location:</span> {meta.location}</div>
        <div className='role'><span className='label'>Role:</span> {meta.duree}</div>
        <div className='startdate'><span className='label'>Start date:</span> {meta.date_de_debut}</div>
        <div className="wp-block-buttons is-layout-flex wp-container-8">
            <div className="wp-block-button is-style-outline">
              <a 
                className="wp-block-button__link wp-element-button pdf-link" 
                target="_blank"
              >
                Learn More
              </a>
            </div>
          <div className="wp-block-button is-style-fill">
            <a 
              className="wp-block-button__link wp-element-button apply-link" 
            >
              Apply
            </a>
          </div>
        </div>
      </div>
    )
  } else {

    return (
      <div { ...useBlockProps( { className: 'career-data' }) }>
        <TextControl
          label={ __( 'Location', 'career-block' ) }
          value={ location }
          onChange={ ( newValue ) => updateMetaValue( newValue, 'location' ) } 
        />
        <TextControl
          label={ __( 'Role', 'career-block' ) }
          value={ duration }
          onChange={ ( newValue ) => updateMetaValue( newValue, 'duration' ) } 
        />
        <TextControl
          label={ __( 'Start Date', 'career-block' ) }
          value={ startdate }
          onChange={ ( newValue ) => updateMetaValue( newValue, 'date_de_debut' ) } 
        />
        <SelectControl
          label={ __( 'Type of contract', 'career-block' ) }
          value={ type }
          options={ [
            { label: 'CDI', value: 'CDI' },
            { label: 'CDD', value: 'CDD' },
            { label: 'Internship', value: 'Internship' },
            { label: 'Freelance', value: 'Freelance' },
          ] }
          onChange={ ( newValue ) => updateMetaValue( newValue, 'type_de_contrat' ) }
        />
        <MediaPlaceholder
          icon={ 'pdf' }
          accept="application/pdf"
          allowedTypes = { [ 'application/pdf' ] }
          onSelect = {
              ( el ) => {
                  updateMetaValue( el.url, 'lien_different' );
              }
          }
          multiple = { false }
          labels = { { title: pdf, instructions: 'Upload or choose a PDF from the Media Library' } }
        >
        </MediaPlaceholder>
      </div>
    );
  }
  
}
