SELECT
    `providerId`,
    `providerName`,
    `ownerId`
FROM
    `provider`
WHERE
    (`listName` LIKE CONCAT('%', :query, '%'))
LIMIT
    :startingIndex, :recordLimit