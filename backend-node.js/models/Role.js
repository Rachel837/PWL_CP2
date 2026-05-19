const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const Role = sequelize.define('Role', {
    nama: {
      type: DataTypes.STRING, 
      allowNull: true
    }
}, {
  tableName: 'roles',
  timestamps: false 
});

module.exports = Role;
