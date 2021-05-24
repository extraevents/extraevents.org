UPDATE competitions SET    
    contact = '@:contact:',
    registration_close = FROM_UNIXTIME(@:registration_close:)
WHERE id = '@:id:'     
