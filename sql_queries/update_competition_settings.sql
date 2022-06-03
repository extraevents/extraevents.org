UPDATE competitions SET    
    contact = '@:contact:',
    nonwca = '@:nonwca:',
    registration_close = FROM_UNIXTIME(@:registration_close:)
WHERE id = '@:id:'     
