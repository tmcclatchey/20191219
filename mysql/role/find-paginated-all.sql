SELECT
    `roleId`,
    `roleName`,
    `roleDescription`,
    `roleFlags`,
    `providerId`
FROM
    `role`
WHERE
    (
        `roleName` LIKE CONCAT('%', :query, '%') OR
        `roleDescription` LIKE CONCAT('%', :query, '%')
    )
LIMIT
    :startingIndex, :recordLimit