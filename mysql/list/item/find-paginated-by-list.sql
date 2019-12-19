SELECT
    `itemId`,
    `itemName`,
    `itemValue`,
    `listId`
FROM
    `list_item`
WHERE
    `listId`=:listId AND
    (
        (`itemName` LIKE CONCAT('%', :query, '%')) OR
        (`itemValue` LIKE CONCAT('%', :query, '%'))
    )
LIMIT
    :startingIndex, :recordLimit