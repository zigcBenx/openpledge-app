/**
 * Converts a string to title case (first letter of each word uppercase, rest lowercase)
 * @param {string} str - The string to convert
 * @returns {string} The converted string
 */
export const toTitleCase = (str) => str.replace(/\w\S*/g, (text) => text.charAt(0).toUpperCase() + text.substring(1).toLowerCase());
