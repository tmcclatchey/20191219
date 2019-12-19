SELECT
    `itemId`,
    `itemName`,
    `itemValue`,
    `listId`
FROM
    `list_item`
LIMIT
    :startingIndex, :recordLimit