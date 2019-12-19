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
        `providerId`=:providerId
    )
    AND
    (
        `roleName` LIKE CONCAT('%', :query, '%') OR
        `roleDescription` LIKE CONCAT('%', :query, '%')
    )
LIMIT
    :startingIndex, :recordLimit