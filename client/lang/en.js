if (typeof ss === 'undefined' || typeof ss.i18n === 'undefined') {
    if (typeof console !== 'undefined') {
      // eslint-disable-line no-console
      console.error('Class ss.i18n not defined'); // eslint-disable-line no-console
    }
  } else {
    ss.i18n.addDictionary('de_DE', {
      "DATAOBJECT_LINKS.LINKLABEL_OBJECT": "Link to an Object",
      "DATAOBJECT_LINKS.LINK_OBJECT": "Link to a DataObject"
    });
  }
