SELECT
    `qaId`,
    `qaQuestion`,
    `qaHash`,
    `qaAdded`,
    `qaUpdated`,
    `userId`
FROM
    `user_qa`
LIMIT
    :startingIndex, :recordLimit