SELECT
    `providerId`,
    `providerName`,
    `ownerId`
FROM
    `provider`
WHERE
    `ownerId`=:ownerId AND 
    (`listName` LIKE CONCAT('%', :query, '%'))
LIMIT
    :startingIndex, :recordLimit