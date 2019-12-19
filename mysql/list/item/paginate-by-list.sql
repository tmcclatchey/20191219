SELECT
    `itemId`,
    `itemName`,
    `itemValue`,
    `listId`
FROM
    `list_item`
WHERE
    `listId`=:listId
LIMIT
    :startingIndex, :recordLimit