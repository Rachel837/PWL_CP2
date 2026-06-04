const { DataTypes } = require('sequelize');
const sequelize = require('../config/database'); 

const DraftPengadaan = sequelize.define('DraftPengadaan', {
    tahun: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    status: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    catatan: {
      type: DataTypes.STRING, 
      allowNull: true
    },
    users_id: {
      type: DataTypes.INTEGER, 
      allowNull: true
    }
}, {
  tableName: 'draft_pengadaan',
  timestamps: true,
  createdAt: 'created_at',
  updatedAt: false
});

// Relationships
const User = require('./User');
const DraftPengadaanDetail = require('./DraftPengadaanDetail');

DraftPengadaan.belongsTo(User, { foreignKey: 'users_id', as: 'pengguna' });
DraftPengadaan.hasMany(DraftPengadaanDetail, { foreignKey: 'draft_pengadaan_id', as: 'details' });

module.exports = DraftPengadaan;
