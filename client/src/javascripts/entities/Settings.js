export default{
    subject: null,
    to: null,
    from: null,
    conditionalTos: [{ field: null, to: null}],

    populate(data) {
        for (var variableKey in data) {
            if (this.hasOwnProperty(variableKey)) {
                this[variableKey] = data[variableKey]
            }
        }
    }
}